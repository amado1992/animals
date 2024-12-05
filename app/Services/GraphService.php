<?php

namespace App\Services;

//use Http;
use App\Models\CurrencyRate;
use App\Models\EmailToken;
use Carbon\Carbon;
use DateTime;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;
use ZipArchive;


class GraphService
{
    private static Client $tokenClient;

    private static string $clientId = '';

    private static string $tenantId = '';

    private static string $graphUserScopes = '';

    private static Graph $userClient;

    private static string $userToken;

    public static function initializeGraphForUserAuth(): void
    {
        self::$tokenClient     = new Client();
        self::$clientId        = env('CLIENT_ID')         ?? 'a055ea92-72b7-4835-8167-07528794bcec';
        self::$tenantId        = env('TENANT_ID')         ?? '16ed3270-1ad0-40f4-a2c6-a7fb899f53a4';
        self::$graphUserScopes = env('GRAPH_USER_SCOPES') ?? 'offline_access user.read mail.read mail.send mail.readwrite mailboxsettings.readwrite contacts.readwrite user.readbasic.all';
        self::$userClient      = new Graph();
    }

    public static function getAllUserToken($acount = null)
    {
        $user_login = Auth::user();
        if (!empty($acount)) {
            $tokenUser = EmailToken::where('user_id', $acount)->get();
        } else {
            $tokenUser = EmailToken::where('user_id', $user_login->id)->get();
        }
        if ($tokenUser->count() > 0) {
            return $tokenUser->toArray();
        } else {
            return [];
        }
    }

    public static function getAllUserTokenByEmail($acount = null)
    {
        $user_login = Auth::user();
        if (!empty($acount)) {
            $tokenUser = EmailToken::where('email', $acount)->get();
        } else {
            $tokenUser = EmailToken::where('email', 'test@zoo-services.com')->get();
        }
        if ($tokenUser->count() > 0) {
            return $tokenUser->toArray();
        } else {
            return [];
        }
    }

    public static function getUserToken($id, $accessToken)
    {
        if (!empty($accessToken)) {
            $date = new DateTime('now');
            $date = $date->format('Y-m-d H:i:s');

            if ($date <= $accessToken->expires_in) {
                return $accessToken->access_token;
            } else {
                $token = self::refresToken($id, $accessToken->refresh_token);

                return $token;
            }
        } else {
            return '';
        }
    }

    public static function getDeviceCode()
    {
        $tokenPath = env('TOKEN_JSON') ?? '/home/forge/app.zoo-services.com/token.json';
        // https://learn.microsoft.com/azure/active-directory/develop/v2-oauth2-device-code
        $deviceCodeRequestUrl = 'https://login.microsoftonline.com/' . self::$tenantId . '/oauth2/v2.0/devicecode';

        // First POST to /devicecode
        $deviceCodeResponse = json_decode(self::$tokenClient->post($deviceCodeRequestUrl, [
            'form_params' => [
                'client_id' => self::$clientId,
                'scope'     => self::$graphUserScopes,
            ],
        ])->getBody()->getContents());

        // Save the token to a file.
        if (!file_exists(dirname($tokenPath))) {
            mkdir(dirname($tokenPath), 0700, true);
        }
        file_put_contents($tokenPath, json_encode($deviceCodeResponse));

        return $deviceCodeResponse;
    }

    public static function authGraph()
    {
        $tokenPath = env('TOKEN_JSON') ?? '/home/forge/app.zoo-services.com/token.json';
        if (file_exists($tokenPath)) {
            $deviceCodeResponse = file_get_contents($tokenPath);
            $deviceCodeResponse = json_decode($deviceCodeResponse, true);
            if (!empty($deviceCodeResponse)) {
                $tokenRequestUrl = 'https://login.microsoftonline.com/' . self::$tenantId . '/oauth2/v2.0/token';
                // Response also indicates how often to poll for completion
                // And gives a device code to send in the polling requests
                $interval    = (int) $deviceCodeResponse['interval'];
                $device_code = $deviceCodeResponse['device_code'];

                // Do polling - if attempt times out the token endpoint
                // returns an error

                while (true) {
                    sleep($interval);
                    // POST to the /token endpoint
                    $tokenResponse = self::$tokenClient->post($tokenRequestUrl, [
                        'form_params' => [
                            'client_id'   => self::$clientId,
                            'grant_type'  => 'urn:ietf:params:oauth:grant-type:device_code',
                            'device_code' => $device_code,
                        ],
                        // These options are needed to enable getting
                        // the response body from a 4xx response
                        'http_errors' => false,
                        'curl'        => [
                            CURLOPT_FAILONERROR => false,
                        ],
                    ]);

                    if ($tokenResponse->getStatusCode() == 200) {
                        $user_login = Auth::user();
                        // Return the access_token
                        $responseBody = json_decode($tokenResponse->getBody()->getContents());
                        $user         = self::getUserMe($responseBody->access_token);
                        $date         = new DateTime('now');
                        $date->modify('+' . $responseBody->expires_in . ' second');

                        $responseBody->expires_in = $date->format('Y-m-d H:i:s');
                        $email_token              = new EmailToken();
                        $email_token['user_id']   = $user_login->id;
                        $email_token['email']     = $user->getMail();
                        $email_token['token']     = json_encode($responseBody);
                        $email_token->save();

                        self::$userToken = $responseBody->access_token;

                        return;
                    } elseif ($tokenResponse->getStatusCode() == 400) {
                        // Check the error in the response body
                        $responseBody = json_decode($tokenResponse->getBody()->getContents());
                        if (isset($responseBody->error)) {
                            $error = $responseBody->error;
                            // authorization_pending means we should keep polling
                            if (strcmp($error, 'authorization_pending') != 0) {
                                throw new Exception('Token endpoint returned ' . $error, 100);
                            }
                        }
                    }
                }
            } else {
                return [];
            }
        } else {
            return [];
        }
    }

    public static function refresToken($id, $refres_token)
    {
        $tokenRequestUrl = 'https://login.microsoftonline.com/' . self::$tenantId . '/oauth2/v2.0/token';
        $tokenResponse   = self::$tokenClient->post($tokenRequestUrl, [
            'form_params' => [
                'client_id'     => self::$clientId,
                'scope'         => self::$graphUserScopes,
                'refresh_token' => $refres_token,
                'grant_type'    => 'refresh_token',
            ],
            // These options are needed to enable getting
            // the response body from a 4xx response
            'http_errors' => false,
            'curl'        => [
                CURLOPT_FAILONERROR => false,
            ],
        ]);

        $responseBody = json_decode($tokenResponse->getBody()->getContents());

        $date = new DateTime('now');
        $date->modify('+' . $responseBody->expires_in . ' second');

        $tokenPath                = env('TOKEN_JSON') ?? '/home/forge/app.zoo-services.com/token.json';
        $responseBody->expires_in = $date->format('Y-m-d H:i:s');
        $email_token              = EmailToken::find($id);
        $email_token['token']     = json_encode($responseBody);
        $email_token->save();

        // Save the token to a file.
        if (!file_exists(dirname($tokenPath))) {
            mkdir(dirname($tokenPath), 0700, true);
        }
        file_put_contents($tokenPath, json_encode($responseBody));
        self::$userToken = $responseBody->access_token;

        return $responseBody->access_token;
    }

    public static function getUnreadInbox($token)
    {
        self::$userClient->setAccessToken($token);

        // Sort by received time, newest first
        $orderBy = '$orderBy=receivedDateTime DESC';
        $select  = '$select=from,isRead,body,toRecipients,receivedDateTime,subject';
        $filter  = 'filter=isread%20eq%20false';

        $requestUrl = '/me/mailFolders/inbox/messages?' . $select . '&' . $orderBy . '&' . $filter;

        return self::$userClient->createCollectionRequest('GET', $requestUrl)
            ->addHeaders(['Prefer' => 'IdType="ImmutableId"'])
            ->setReturnType(Model\Message::class)
            ->setPageSize(25);
    }

    public static function updateIsReadEmailInbox($token, $id_user, $id, $isRead = true)
    {
        self::$userClient->setAccessToken($token);

        $requestBody = new Model\Message();

        $requestBody->setIsRead($isRead);

        $requestUrl = '/users/' . $id_user . '/messages/' . $id;

        return self::$userClient->createRequest('PATCH', $requestUrl)
            ->attachBody($requestBody)
            ->execute();
    }

    public static function getAllInbox($token)
    {
        self::$userClient->setAccessToken($token);

        // Only request specific properties
        $select = '$select=from,isRead,sender,receivedDateTime,subject';
        // Sort by received time, newest first
        $orderBy = '$orderBy=receivedDateTime DESC';
        $select  = '$select=from,isRead,receivedDateTime,subject';

        $requestUrl = '/me/mailFolders/inbox/messages?' . $select . '&' . $orderBy;

        return self::$userClient->createCollectionRequest('GET', $requestUrl)
            ->addHeaders(['Prefer' => 'IdType="ImmutableId"'])
            ->setReturnType(Model\Message::class)
            ->setPageSize(25);
    }

    public static function updateCategorieContact($token, $contact)
    {
        self::$userClient->setAccessToken($token);

        $requestBody = new Model\Contact();

        $requestBody->setCategories(['Categoría naranja']);

        return self::$userClient->createRequest('PATCH', '/me/contacts/' . $contact->getId())
            ->attachBody($requestBody)
            ->execute();
    }

    public static function getContact($token)
    {
        self::$userClient->setAccessToken($token);

        return self::$userClient->createRequest('GET', '/me/contacts')
            ->setReturnType(Model\Contact::class)
            ->execute();
    }

    public static function setContact($token, $contact)
    {
        if (!empty($contact['email'])) {
            self::$userClient->setAccessToken($token);

            $requestBody = new Model\Contact();

            $requestBody->setGivenName($contact['first_name'] ?? 'Customer');

            $requestBody->setSurname($contact['last_name'] ?? 'Default');

            $emailAddressesEmailAddress1 = new Model\EmailAddress();
            $emailAddressesEmailAddress1->setAddress($contact['email']);

            $emailAddressesEmailAddress1->setName($contact['first_name'] ?? 'Customer' . ' ' . $contact['last_name'] ?? 'Default');

            $emailAddressesArray[] = $emailAddressesEmailAddress1;
            $requestBody->setEmailAddresses($emailAddressesArray);

            return self::$userClient->createRequest('POST', '/me/contacts')
                ->attachBody($requestBody)
                ->execute();
        }
    }

    public static function getUserMe($token)
    {
        self::$userClient->setAccessToken($token);
        $requestUrl = '/me';

        return self::$userClient->createRequest('GET', $requestUrl)
            ->setReturnType(Model\User::class)
            ->execute();
    }

    public static function getUserByEmail($token, $email)
    {
        self::$userClient->setAccessToken($token);

        $requestUrl  = '/users';
        $user_refult = self::$userClient->createCollectionRequest('GET', $requestUrl)
            ->setReturnType(Model\User::class)
            ->setPageSize(100);

        foreach ($user_refult->getPage() as $row) {
            if (!empty($row->getMail()) && $row->getMail() == $email) {
                return $row;
            }
        }
    }

    public static function getUnreadInboxByUser($token, $id, $created_at, $directory)
    {
        try {
            self::$userClient->setAccessToken($token);

            // Sort by received time, newest first
            if (!empty($created_at)) {
                $orderBy    = '$orderBy=receivedDateTime ASC';
                $select     = '$select=from,isRead,body,toRecipients,ccRecipients,bccRecipients,receivedDateTime,subject,hasAttachments,createdDateTime,lastModifiedDateTime';
                $filter     = 'filter=receivedDateTime%20ge%20' . $created_at;
                $requestUrl = '/users/' . $id . '/mailFolders/' . $directory . '/messages?' . $select . '&' . $orderBy . '&' . $filter;
            } else {
                $orderBy    = '$orderBy=receivedDateTime DESC';
                $select     = '$select=from,isRead,body,toRecipients,ccRecipients,bccRecipients,receivedDateTime,subject,hasAttachments,createdDateTime,lastModifiedDateTime';
                $requestUrl = '/users/' . $id . '/mailFolders/' . $directory . '/messages?' . $select . '&' . $orderBy;
            }
            $result = self::$userClient->createCollectionRequest('GET', $requestUrl)
                ->addHeaders(['Prefer' => 'IdType="ImmutableId"'])
                ->setReturnType(Model\Message::class)
                ->setPageSize(25);

            return $result;
        } catch (\Throwable $th) {
            return;
        }
    }

    public static function getUnreadInboxByUserText($token, $id, $created_at, $directory) {
        try {
            GraphService::$userClient->setAccessToken($token);

            // Sort by received time, newest first
            if(!empty($created_at)){
                $orderBy = '$orderBy=receivedDateTime ASC';
                $select = '$select=from,isRead,body,toRecipients,ccRecipients,bccRecipients,receivedDateTime,subject,hasAttachments,createdDateTime,lastModifiedDateTime';
                $filter='filter=receivedDateTime%20ge%20' . $created_at;
                $requestUrl = '/users/' . $id . '/mailFolders/' . $directory . '/messages?'.$select.'&'.$orderBy.'&'.$filter;
            }else{
                $orderBy = '$orderBy=receivedDateTime DESC';
                $select = '$select=from,isRead,body,toRecipients,ccRecipients,bccRecipients,receivedDateTime,subject,hasAttachments,createdDateTime,lastModifiedDateTime';
                $requestUrl = '/users/' . $id . '/mailFolders/' . $directory . '/messages?'.$select.'&'.$orderBy;
            }
            $result = GraphService::$userClient->createCollectionRequest('GET', $requestUrl)
                                                ->addHeaders(array("Prefer" => 'IdType="ImmutableId",outlook.body-content-type="text"'))
                                                ->setReturnType(Model\Message::class)
                                                ->setPageSize(25);
            return $result;

        } catch (\Throwable $th) {
            return;
        }
    }

    public static function getAllInboxByUser($token, $id, $created_at) {
        try {
            self::$userClient->setAccessToken($token);

            // Sort by received time, newest first
            $orderBy = '$orderBy=receivedDateTime ASC';
            $select  = '$select=from,isRead,isDraft,parentFolderId,toRecipients,receivedDateTime,subject,hasAttachments';
            $filter  = 'filter=receivedDateTime%20ge%20' . $created_at;

            $requestUrl = '/users/' . $id . '/mailFolders/inbox/messages?' . $select . '&' . $orderBy . '&' . $filter;
            $result     = self::$userClient->createCollectionRequest('GET', $requestUrl)
                ->addHeaders(['Prefer' => 'IdType="ImmutableId"'])
                ->setReturnType(Model\Message::class)
                ->setPageSize(100);

            return $result;
        } catch (\Throwable $th) {
            return;
        }
    }

    public static function getAllInboxByUserDirectory($token, $id, $created_at, $directory)
    {
        try {
            self::$userClient->setAccessToken($token);

            // Sort by received time, newest first
            $orderBy = '$orderBy=receivedDateTime ASC';
            $select  = '$select=from,isRead,isDraft,parentFolderId,toRecipients,receivedDateTime,subject,hasAttachments';
            $filter  = 'filter=receivedDateTime%20ge%20' . $created_at;

            $requestUrl = '/users/' . $id . '/mailFolders/' . $directory . '/messages?' . $select . '&' . $orderBy . '&' . $filter;

            $result = self::$userClient->createCollectionRequest('GET', $requestUrl)
                ->addHeaders(['Prefer' => 'IdType="ImmutableId"'])
                ->setReturnType(Model\Message::class)
                ->setPageSize(25);

            return $result;
        } catch (\Throwable $th) {
            return;
        }
    }

    public static function getAttachmentsByEmail($token, $id, $id_menssage)
    {
        try {
            self::$userClient->setAccessToken($token);

            $select = '$select=name,contentType,isInline,id,size';

            $requestUrl = '/users/' . $id . '/mailFolders/inbox/messages/' . $id_menssage . '/attachments?$expand=microsoft.graph.itemattachment/item&' . $select;
            $result     = self::$userClient->createCollectionRequest('GET', $requestUrl)
                ->addHeaders(['Prefer' => 'IdType="ImmutableId"'])
                ->setReturnType(Model\FileAttachment::class)
                ->setPageSize(25);

            return $result;
        } catch (\Throwable $th) {
            return;
        }
    }

    public static function getAttachmentsInfoByEmail($token, $id, $id_menssage, $id_attachments)
    {
        try {
            self::$userClient->setAccessToken($token);

            $requestUrl = '/users/' . $id . '/mailFolders/inbox/messages/' . $id_menssage . '/attachments/' . $id_attachments . '/?$expand=microsoft.graph.itemattachment/item';
            $result     = self::$userClient->createCollectionRequest('GET', $requestUrl)
                ->addHeaders(['Prefer' => 'IdType="ImmutableId"'])
                ->setReturnType(Model\FileAttachment::class)
                ->setPageSize(25);

            return $result;
        } catch (\Throwable $th) {
            return;
        }
    }

    public static function getAllMailFolders($token, $id)
    {
        try {
            self::$userClient->setAccessToken($token);

            $requestUrl = '/users/' . $id . '/mailFolders';
            $result     = self::$userClient->createCollectionRequest('GET', $requestUrl)
                ->addHeaders(['Prefer' => 'IdType="ImmutableId"'])
                ->setReturnType(Model\MailFolder::class)
                ->setPageSize(100);

            return $result;
        } catch (\Throwable $th) {
            return;
        }
    }

    public static function getFolders($token, $id, $id_folder)
    {
        try {
            self::$userClient->setAccessToken($token);

            $requestUrl = '/users/' . $id . '/mailFolders/' . $id_folder;
            $result     = self::$userClient->createCollectionRequest('GET', $requestUrl)
                ->addHeaders(['Prefer' => 'IdType="ImmutableId"'])
                ->setReturnType(Model\MailFolder::class)
                ->setPageSize(100);

            return $result->getPage();
        } catch (\Throwable $th) {
            return;
        }
    }

    public static function getFoldersName($token, $id, $id_folder)
    {
        try {
            self::$userClient->setAccessToken($token);

            $requestUrl = '/users/' . $id . '/mailFolders/' . $id_folder;
            $result     = self::$userClient->createCollectionRequest('GET', $requestUrl)
                ->addHeaders(['Prefer' => 'IdType="ImmutableId"'])
                ->setReturnType(Model\MailFolder::class)
                ->setPageSize(100);

            return $result->getPage();
        } catch (\Throwable $th) {
            return;
        }
    }

    public static function getEmailInfo($token, $id_user, $id)
    {
        try {
            self::$userClient->setAccessToken($token);

            $select = '$select=from,isRead,body,toRecipients,lastModifiedDateTime,subject';

            $requestUrl = '/users/' . $id_user . '/messages/' . $id . '?' . $select;

            $result = self::$userClient->createCollectionRequest('GET', $requestUrl)
                ->addHeaders(['Prefer' => 'IdType="ImmutableId"'])
                ->setReturnType(Model\Message::class)
                ->setPageSize(25);

            return $result->getPage();
        } catch (\Throwable $th) {
            return 'false';
        }
    }

    public static function getEmailInfoText($token, $id_user, $id) {

        try {
            GraphService::$userClient->setAccessToken($token);

            $select = '$select=body';

            $requestUrl = '/users/' . $id_user . '/messages/' . $id . '?' . $select;

            $result = GraphService::$userClient->createCollectionRequest('GET', $requestUrl)
                                                ->addHeaders(array("Prefer" => 'IdType="ImmutableId",outlook.body-content-type="text"'))
                                                ->setReturnType(Model\Message::class)
                                                ->setPageSize(25);

            return $result->getPage();

        } catch (\Throwable $th) {
            return "false";
        }
    }

    public static function getEmailInfoMIME($token, $id_user, $id) {

        try {
            GraphService::$userClient->setAccessToken($token);

            $requestUrl = '/users/' . $id_user . '/messages/' . $id . '/$value';

            $result = GraphService::$userClient->createRequest('GET', $requestUrl)
                                                ->execute();

            return $result;
        } catch (\Throwable $th) {
            return;
        }
    }

    public static function getEmailInfoInbox($token, $id_user, $id) {
        GraphService::$userClient->setAccessToken($token);

        $select = '$select=from,isRead,body,toRecipients,lastModifiedDateTime,subject';

        $requestUrl = '/users/' . $id_user . '/mailFolders/inbox/messages/' . $id . '?' . $select;
        $result     = self::$userClient->createCollectionRequest('GET', $requestUrl)
            ->addHeaders(['Prefer' => 'IdType="ImmutableId"'])
            ->setReturnType(Model\Message::class)
            ->setPageSize(100);

        return $result;
    }

    public static function getEmailInfoFolder($token, $id_folder, $id_user, $id)
    {
        try {
            self::$userClient->setAccessToken($token);

            $select = '$select=from,isRead,toRecipients,lastModifiedDateTime,subject,parentFolderId';

            $requestUrl = '/users/' . $id_user . '/mailFolders/' . $id_folder . '/messages/' . $id . '?' . $select;

            $result = self::$userClient->createCollectionRequest('GET', $requestUrl)
                ->addHeaders(['Prefer' => 'IdType="ImmutableId"'])
                ->setReturnType(Model\Message::class)
                ->setPageSize(100);

            return $result->getPage();
        } catch (\Throwable $th) {
            return;
        }
    }

    public static function updateArchive($token, $id_user, $id_message)
    {
        try {
            self::$userClient->setAccessToken($token);

            $requestBody = ['destinationId' => 'archive'];

            $requestUrl = '/users/' . $id_user . '/messages/' . $id_message . '/move';

            return self::$userClient->createCollectionRequest('POST', $requestUrl)
                ->addHeaders(['Prefer' => 'IdType="ImmutableId"'])
                ->attachBody($requestBody)
                ->setPageSize(1)->getPage();
        } catch (\Throwable $th) {
            return;
        }
    }

    public static function updateDelete($token, $id_user, $id_message)
    {
        try {
            self::$userClient->setAccessToken($token);

            $requestBody = ['destinationId' => 'deleteditems'];

            $requestUrl = '/users/' . $id_user . '/messages/' . $id_message . '/move';

            return self::$userClient->createCollectionRequest('POST', $requestUrl)
                ->addHeaders(['Prefer' => 'IdType="ImmutableId"'])
                ->attachBody($requestBody)
                ->setPageSize(1)->getPage();
        } catch (\Throwable $th) {
            return;
        }
    }

    public static function updateSpam($token, $id_user, $id_message) {
        try {
            GraphService::$userClient->setAccessToken($token);

            $requestBody = array("destinationId" => "junkemail");

            $requestUrl = '/users/' . $id_user . '/messages/' . $id_message . '/move';

            return GraphService::$userClient->createCollectionRequest('POST', $requestUrl)
                                        ->addHeaders(array("Prefer" => 'IdType="ImmutableId"'))
                                        ->attachBody($requestBody)
                                        ->setPageSize(1)->getPage();
        } catch (\Throwable $th) {
            return;
        }
    }

    public static function sendEmail($token, $id_user, $subject, $body, $email_to, $email_cc, $email_bcc, $email_attachment) {
        try {
            self::$userClient->setAccessToken($token);

            //Subject
            $message = new Model\Message();
            $message->setSubject($subject);

            //Body
            $messageBody = new Model\ItemBody();
            $messageBody->setContentType(new Model\BodyType('html'));
            $messageBody->setContent($body);

            $message->setBody($messageBody);

            if(!empty($email_to)){
                //Email CC
                foreach($email_to as $key => $row){
                    if($row != ""){
                        $toRecipientsRecipient1 = new Model\Recipient();
                        $toRecipientsRecipient1EmailAddress = new Model\EmailAddress();
                        $toRecipientsRecipient1EmailAddress->setAddress($row);
                        $toRecipientsRecipient1->setEmailAddress($toRecipientsRecipient1EmailAddress);
                        $toRecipientsArray[$key]= $toRecipientsRecipient1;
                    }
                }
                $message->setToRecipients($toRecipientsArray);
            }

            if (!empty($email_cc)) {
                //Email CC
                foreach ($email_cc as $key => $row) {
                    if ($row != '') {
                        $ccRecipientsRecipient1             = new Model\Recipient();
                        $ccRecipientsRecipient1EmailAddress = new Model\EmailAddress();
                        $ccRecipientsRecipient1EmailAddress->setAddress($row);
                        $ccRecipientsRecipient1->setEmailAddress($ccRecipientsRecipient1EmailAddress);
                        $ccRecipientsArray[$key] = $ccRecipientsRecipient1;
                    }
                }
                $message->setCcRecipients($ccRecipientsArray);
            }

            if (!empty($email_bcc)) {
                //Email CC

                foreach ($email_bcc as $key => $row) {
                    if ($row != '') {
                        $bccRecipientsRecipient1             = new Model\Recipient();
                        $bccRecipientsRecipient1EmailAddress = new Model\EmailAddress();
                        $bccRecipientsRecipient1EmailAddress->setAddress($row);
                        $bccRecipientsRecipient1->setEmailAddress($bccRecipientsRecipient1EmailAddress);
                        $bccRecipientsArray[$key] = $bccRecipientsRecipient1;
                    }
                }
                $message->setBCcRecipients($bccRecipientsArray);
            }

            if (!empty($email_attachment)) {
                //Email Attachment
                foreach ($email_attachment as $key => $row) {
                    if ($row != '') {
                        $attachmentsAttachment1 = new Model\FileAttachment();
                        $attachmentsAttachment1->setODataType('#microsoft.graph.fileAttachment');
                        $attachmentsAttachment1->setName($row['name']);
                        $attachmentsAttachment1->setContentType($row['type']);
                        $attachmentsAttachment1->setContentBytes(base64_encode($row['content']));
                        $attachmentsArray[$key] = $attachmentsAttachment1;
                    }
                }
                $message->setAttachments($attachmentsArray);
            }

            $message_array = ['message' => $message];

            $requestUrl = '/users/' . $id_user . '/sendMail';

            $result = self::$userClient->createCollectionRequest('POST', $requestUrl)
                ->addHeaders(['Prefer' => 'IdType="ImmutableId"'])
                ->attachBody($message_array)
                ->setPageSize(1)->getPage();
            if (empty($result)) {
                return true;
            } else {
                return false;
            }
        } catch (\Throwable $th) {
            return false;
        }
    }

    public static function replyEmail($token, $id_user, $id_email, $subject, $body, $email_to, $email_cc, $email_bcc)
    {
        self::$userClient->setAccessToken($token);

        //Subject
        $message = new Model\Message();
        $message->setSubject($subject);

        //Body
        $messageBody = new Model\ItemBody();
        $messageBody->setContentType(new Model\BodyType('html'));
        $messageBody->setContent($body);

        $message->setBody($messageBody);

        if(!empty($email_to)){
            //Email CC
            foreach($email_to as $key => $row){
                if($row != ""){
                    $toRecipientsRecipient1 = new Model\Recipient();
                    $toRecipientsRecipient1EmailAddress = new Model\EmailAddress();
                    $toRecipientsRecipient1EmailAddress->setAddress($row);
                    $toRecipientsRecipient1->setEmailAddress($toRecipientsRecipient1EmailAddress);
                    $toRecipientsArray[$key]= $toRecipientsRecipient1;
                }
            }
            $message->setToRecipients($toRecipientsArray);
        }

        if (!empty($email_cc)) {
            //Email CC
            $ccRecipientsRecipient1             = new Model\Recipient();
            $ccRecipientsRecipient1EmailAddress = new Model\EmailAddress();
            $ccRecipientsRecipient1EmailAddress->setAddress($email_cc);
            $ccRecipientsRecipient1->setEmailAddress($ccRecipientsRecipient1EmailAddress);
            $ccRecipientsArray[] = $ccRecipientsRecipient1;
            $message->setCcRecipients($ccRecipientsArray);
        }

        if (!empty($email_bcc)) {
            //Email CC

            foreach ($email_bcc as $key => $row) {
                if ($row != '') {
                    $bccRecipientsRecipient1             = new Model\Recipient();
                    $bccRecipientsRecipient1EmailAddress = new Model\EmailAddress();
                    $bccRecipientsRecipient1EmailAddress->setAddress($row);
                    $bccRecipientsRecipient1->setEmailAddress($bccRecipientsRecipient1EmailAddress);
                    $bccRecipientsArray[$key] = $bccRecipientsRecipient1;
                }
            }
            $message->setBCcRecipients($bccRecipientsArray);
        }

        $message_array = ['message' => $message];

        $requestUrl = '/users/' . $id_user . '/messages/' . $id_email . '/reply';

        $result = self::$userClient->createCollectionRequest('POST', $requestUrl)
            ->addHeaders(['Prefer' => 'IdType="ImmutableId"'])
            ->attachBody($message_array)
            ->setPageSize(1)->getPage();
        if (empty($result)) {
            return true;
        } else {
            return false;
        }
    }

    public static function forwardEmail($token, $id_user, $id_email, $subject, $body, $email_to, $email_cc, $email_bcc)
    {
        try {
            self::$userClient->setAccessToken($token);

            //Subject
            $message = new Model\Message();
            $message->setSubject($subject);

            //Body
            $messageBody = new Model\ItemBody();
            $messageBody->setContentType(new Model\BodyType('html'));
            $messageBody->setContent($body);

            $message->setBody($messageBody);

            if(!empty($email_to)){
                //Email CC
                foreach($email_to as $key => $row){
                    if($row != ""){
                        $toRecipientsRecipient1 = new Model\Recipient();
                        $toRecipientsRecipient1EmailAddress = new Model\EmailAddress();
                        $toRecipientsRecipient1EmailAddress->setAddress($row);
                        $toRecipientsRecipient1->setEmailAddress($toRecipientsRecipient1EmailAddress);
                        $toRecipientsArray[$key]= $toRecipientsRecipient1;
                    }
                }
                $message->setToRecipients($toRecipientsArray);
            }

            if (!empty($email_cc)) {
                //Email CC
                foreach ($email_cc as $key => $row) {
                    if ($row != '') {
                        $ccRecipientsRecipient1             = new Model\Recipient();
                        $ccRecipientsRecipient1EmailAddress = new Model\EmailAddress();
                        $ccRecipientsRecipient1EmailAddress->setAddress($row);
                        $ccRecipientsRecipient1->setEmailAddress($ccRecipientsRecipient1EmailAddress);
                        $ccRecipientsArray[$key] = $ccRecipientsRecipient1;
                    }
                }
                $message->setCcRecipients($ccRecipientsArray);
            }

            if (!empty($email_bcc)) {
                //Email CC

                foreach ($email_bcc as $key => $row) {
                    if ($row != '') {
                        $bccRecipientsRecipient1             = new Model\Recipient();
                        $bccRecipientsRecipient1EmailAddress = new Model\EmailAddress();
                        $bccRecipientsRecipient1EmailAddress->setAddress($row);
                        $bccRecipientsRecipient1->setEmailAddress($bccRecipientsRecipient1EmailAddress);
                        $bccRecipientsArray[$key] = $bccRecipientsRecipient1;
                    }
                }
                $message->setBCcRecipients($bccRecipientsArray);
            }

            $message_array = ['message' => $message];

            $requestUrl = '/users/' . $id_user . '/messages/' . $id_email . '/forward';

            $result = self::$userClient->createCollectionRequest('POST', $requestUrl)
                ->addHeaders(['Prefer' => 'IdType="ImmutableId"'])
                ->attachBody($message_array)
                ->setPageSize(1)->getPage();
            if (empty($result)) {
                return true;
            } else {
                return false;
            }
        } catch (\Throwable $th) {
            return false;
        }
    }

    public static function saveDraft($token, $id_user, $subject, $body, $email_to, $email_cc, $email_bcc, $email_attachment)
    {
        try {
            self::$userClient->setAccessToken($token);

            //Subject
            $message = new Model\Message();
            $message->setSubject($subject);

            $message->setImportance(new Model\Importance('low'));

            //Body
            $messageBody = new Model\ItemBody();
            $messageBody->setContentType(new Model\BodyType('html'));
            $messageBody->setContent($body);

            $message->setBody($messageBody);

            if(!empty($email_to)){
                //Email CC
                foreach($email_to as $key => $row){
                    if($row != ""){
                        $toRecipientsRecipient1 = new Model\Recipient();
                        $toRecipientsRecipient1EmailAddress = new Model\EmailAddress();
                        $toRecipientsRecipient1EmailAddress->setAddress($row);
                        $toRecipientsRecipient1->setEmailAddress($toRecipientsRecipient1EmailAddress);
                        $toRecipientsArray[$key]= $toRecipientsRecipient1;
                    }
                }
                $message->setToRecipients($toRecipientsArray);
            }

            if (!empty($email_cc)) {
                //Email CC
                foreach ($email_cc as $key => $row) {
                    if ($row != '') {
                        $ccRecipientsRecipient1             = new Model\Recipient();
                        $ccRecipientsRecipient1EmailAddress = new Model\EmailAddress();
                        $ccRecipientsRecipient1EmailAddress->setAddress($row);
                        $ccRecipientsRecipient1->setEmailAddress($ccRecipientsRecipient1EmailAddress);
                        $ccRecipientsArray[$key] = $ccRecipientsRecipient1;
                    }
                }
                $message->setCcRecipients($ccRecipientsArray);
            }

            if (!empty($email_bcc)) {
                //Email CC

                foreach ($email_bcc as $key => $row) {
                    if ($row != '') {
                        $bccRecipientsRecipient1             = new Model\Recipient();
                        $bccRecipientsRecipient1EmailAddress = new Model\EmailAddress();
                        $bccRecipientsRecipient1EmailAddress->setAddress($row);
                        $bccRecipientsRecipient1->setEmailAddress($bccRecipientsRecipient1EmailAddress);
                        $bccRecipientsArray[$key] = $bccRecipientsRecipient1;
                    }
                }
                $message->setBCcRecipients($bccRecipientsArray);
            }

            if (!empty($email_attachment)) {
                //Email Attachment
                foreach ($email_attachment as $key => $row) {
                    if ($row != '') {
                        $attachmentsAttachment1 = new Model\FileAttachment();
                        $attachmentsAttachment1->setODataType('#microsoft.graph.fileAttachment');
                        $attachmentsAttachment1->setName($row['name']);
                        $attachmentsAttachment1->setContentType($row['type']);
                        $attachmentsAttachment1->setContentBytes(base64_encode($row['content']));
                        $attachmentsArray[$key] = $attachmentsAttachment1;
                    }
                }
                $message->setAttachments($attachmentsArray);
            }

            $requestUrl = '/users/' . $id_user . '/mailFolders/drafts/messages';

            $result = self::$userClient->createCollectionRequest('POST', $requestUrl)
                ->addHeaders(['Prefer' => 'IdType="ImmutableId"'])
                ->attachBody($message)
                ->setPageSize(1)->getPage();
            if (!empty($result)) {
                return $result;
            } else {
                return false;
            }
        } catch (\Throwable $th) {
            return false;
        }
    }

    public static function saveSentItems($token, $id_user, $subject, $body, $email_to, $email_cc, $email_bcc,  $email_attachment) {

        try {
            GraphService::$userClient->setAccessToken($token);

            //Subject
            $message = new Model\Message();
            $message->setSubject($subject);

            //Body
            $messageBody = new Model\ItemBody();
            $messageBody->setContentType(new Model\BodyType('html'));
            $messageBody->setContent($body);

            $message->setBody($messageBody);

            if(!empty($email_to)){
                //Email CC
                foreach($email_to as $key => $row){
                    if($row != ""){
                        $toRecipientsRecipient1 = new Model\Recipient();
                        $toRecipientsRecipient1EmailAddress = new Model\EmailAddress();
                        $toRecipientsRecipient1EmailAddress->setAddress($row);
                        $toRecipientsRecipient1->setEmailAddress($toRecipientsRecipient1EmailAddress);
                        $toRecipientsArray[$key]= $toRecipientsRecipient1;
                    }
                }
                $message->setToRecipients($toRecipientsArray);
            }

            if(!empty($email_cc)){
                //Email CC
                foreach($email_cc as $key => $row){
                    if($row != ""){
                        $ccRecipientsRecipient1 = new Model\Recipient();
                        $ccRecipientsRecipient1EmailAddress = new Model\EmailAddress();
                        $ccRecipientsRecipient1EmailAddress->setAddress($row);
                        $ccRecipientsRecipient1->setEmailAddress($ccRecipientsRecipient1EmailAddress);
                        $ccRecipientsArray[$key]= $ccRecipientsRecipient1;
                    }
                }
                $message->setCcRecipients($ccRecipientsArray);
            }

            if(!empty($email_bcc)){
                //Email CC


                foreach($email_bcc as $key => $row){
                    if($row != ""){
                        $bccRecipientsRecipient1 = new Model\Recipient();
                        $bccRecipientsRecipient1EmailAddress = new Model\EmailAddress();
                        $bccRecipientsRecipient1EmailAddress->setAddress($row);
                        $bccRecipientsRecipient1->setEmailAddress($bccRecipientsRecipient1EmailAddress);
                        $bccRecipientsArray[$key]= $bccRecipientsRecipient1;
                    }
                }
                $message->setBCcRecipients($bccRecipientsArray);
            }

            if(!empty($email_attachment)){
                //Email Attachment
                foreach($email_attachment as $key => $row){
                    if($row != ""){
                        $attachmentsAttachment1 = new Model\FileAttachment();
                        $attachmentsAttachment1->setODataType('#microsoft.graph.fileAttachment');
                        $attachmentsAttachment1->setName($row["name"]);
                        $attachmentsAttachment1->setContentType($row["type"]);
                        $attachmentsAttachment1->setContentBytes(base64_encode($row["content"]));
                        $attachmentsArray[$key]= $attachmentsAttachment1;
                    }
                }
                $message->setAttachments($attachmentsArray);
            }

            $requestUrl = '/users/' . $id_user . '/mailFolders/sentitems/messages';

            $result = GraphService::$userClient->createCollectionRequest('POST', $requestUrl)
                                                ->addHeaders(array("Prefer" => 'IdType="ImmutableId"'))
                                                ->attachBody($message)
                                                ->setPageSize(1)->getPage();
            if(!empty($result)){
                return $result;
            }else{
                return false;
            }
        } catch (\Throwable $th) {
            return false;
        }
    }

    public static function saveEmailSystem($token, $id_user, $email_to, $options, $email_cc = null, $email_bcc = null) {
        try {

            GraphService::$userClient->setAccessToken($token);

            //Subject
            $message = new Model\Message();
            $message->setSubject($options->email_subject);

            //Body
            $messageBody = new Model\ItemBody();
            $messageBody->setContentType(new Model\BodyType('html'));
            $messageBody->setContent($options->email_content);

            $message->setBody($messageBody);

            if(!empty($email_to)){
                //Email CC
                foreach($email_to as $key => $row){
                    if($row != ""){
                        $toRecipientsRecipient1 = new Model\Recipient();
                        $toRecipientsRecipient1EmailAddress = new Model\EmailAddress();
                        $toRecipientsRecipient1EmailAddress->setAddress($row);
                        $toRecipientsRecipient1->setEmailAddress($toRecipientsRecipient1EmailAddress);
                        $toRecipientsArray[$key]= $toRecipientsRecipient1;
                    }
                }
                $message->setToRecipients($toRecipientsArray);
            }

            if(!empty($email_cc)){
                 //Email CC
                foreach($email_cc as $key => $row){
                    if($row != ""){
                        $ccRecipientsRecipient1 = new Model\Recipient();
                        $ccRecipientsRecipient1EmailAddress = new Model\EmailAddress();
                        $ccRecipientsRecipient1EmailAddress->setAddress($row);
                        $ccRecipientsRecipient1->setEmailAddress($ccRecipientsRecipient1EmailAddress);
                        $ccRecipientsArray[$key]= $ccRecipientsRecipient1;
                    }
                }
                $message->setCcRecipients($ccRecipientsArray);
            }

            if(!empty($email_bcc)){
                //Email CC


                foreach($email_bcc as $key => $row){
                    if($row != ""){
                        $bccRecipientsRecipient1 = new Model\Recipient();
                        $bccRecipientsRecipient1EmailAddress = new Model\EmailAddress();
                        $bccRecipientsRecipient1EmailAddress->setAddress($row);
                        $bccRecipientsRecipient1->setEmailAddress($bccRecipientsRecipient1EmailAddress);
                        $bccRecipientsArray[$key]= $bccRecipientsRecipient1;
                    }
                }
                $message->setBCcRecipients($bccRecipientsArray);
            }

            if(!empty($options->attachment)){
                //Email Attachment
                $attachmentsAttachment1 = new Model\FileAttachment();
                $attachmentsAttachment1->setODataType('#microsoft.graph.fileAttachment');
                $attachmentsAttachment1->setName($options->attachment["name"]);
                $attachmentsAttachment1->setContentType($options->attachment["type"]);
                $attachmentsAttachment1->setContentBytes(base64_encode(file_get_contents(Storage::disk('')->path($options->attachment["path"]))));
                $attachmentsArray[0]= $attachmentsAttachment1;

                $message->setAttachments($attachmentsArray);
            }


            $message_array = array("message" => $message);

            $requestUrl = '/users/' . $id_user . '/mailFolders/sentitems/messages';

            $result = GraphService::$userClient->createCollectionRequest('POST', $requestUrl)
                                                ->addHeaders(array("Prefer" => 'IdType="ImmutableId"'))
                                                ->attachBody($message)
                                                ->setPageSize(1)->getPage();
            if(!empty($result)){
                return $result;
            }else{
                return false;
            }
        } catch (\Throwable $th) {
            return false;
        }
    }

    public static function sendEmailDraft($token, $id_user, $id_message) {
        try {
            GraphService::$userClient->setAccessToken($token);

            $requestUrl = '/users/' . $id_user . '/messages/' . $id_message . '/send';

            return GraphService::$userClient->createCollectionRequest('POST', $requestUrl)
                                        ->addHeaders(array("Prefer" => 'IdType="ImmutableId"'))
                                        ->setPageSize(1)->getPage();
        } catch (\Throwable $th) {
            return;
        }
    }

    public static function updateIsDraftEmailInbox($token, $id_user, $id) {
        GraphService::$userClient->setAccessToken($token);

        $requestBody = new Model\Message();

        $requestBody->setIsDraft(false);

        $requestUrl = '/users/' . $id_user . '/messages/'.$id;
        return GraphService::$userClient->createRequest('PATCH', $requestUrl)
                                       ->attachBody($requestBody)
                                       ->execute();
    }

    public static function saveDraftForward($token, $id_user, $id_message) {
        try {
            self::$userClient->setAccessToken($token);

            $requestUrl = '/users/' . $id_user . '/mailFolders/drafts/messages/' . $id_message . '/createForward';

            return self::$userClient->createCollectionRequest('POST', $requestUrl)
                ->addHeaders(['Prefer' => 'IdType="ImmutableId"'])
                ->setPageSize(1)->getPage();
        } catch (\Throwable $th) {
            return;
        }
    }
}
