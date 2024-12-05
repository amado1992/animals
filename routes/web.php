<?php

use App\Mail\TestMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::middleware('auth')->group(function () {
    Route::get('/', 'HomeController@index')->name('home');

    Route::prefix('our-surplus')->name('our-surplus.')->group(function () {
        Route::post('delete_items', 'OurSurplusController@delete_items')->name('deleteItems');
        Route::post('upload_file', 'OurSurplusController@upload_file')->name('upload');
        Route::post('selectOurSurplusList', 'OurSurplusController@selectOurSurplusList')->name('selectOurSurplusList');
        Route::post('saveOurSurplusList', 'OurSurplusController@saveOurSurplusList')->name('saveOurSurplusList');
        Route::post('deleteOurSurplusList', 'OurSurplusController@deleteOurSurplusList')->name('deleteOurSurplusList');
        Route::post('editSelectedRecords', 'OurSurplusController@editSelectedRecords')->name('editSelectedRecords');
        Route::get('filterOurSurplus', 'OurSurplusController@filterOurSurplus')->name('filterOurSurplus');
        Route::get('printOurSurplusList', 'OurSurplusController@printOurSurplusList')->name('printOurSurplusList');
        Route::get('delete_file/{oursurplus_id}/{file_name}', 'OurSurplusController@delete_file')->name('delete_file');
        Route::get('showAll', 'OurSurplusController@showAll')->name('showAll');
        Route::get('orderBy', 'OurSurplusController@orderBy')->name('orderBy');
        Route::get('removeFromOurSurplusSession/{key}', 'OurSurplusController@removeFromOurSurplusSession')->name('removeFromOurSurplusSession');
        Route::post('searchRelatedSurplusSuppliers', 'OurSurplusController@searchRelatedSurplusSuppliers')->name('searchRelatedSurplusSuppliers');
        Route::get('export', 'OurSurplusController@export')->name('export');
        Route::post('checkSameRecord', 'OurSurplusController@checkSameRecord')->name('checkSameRecord');
        Route::get('recordsPerPage', 'OurSurplusController@recordsPerPage')->name('recordsPerPage');
        Route::post('calculateCostPercentage', 'OurSurplusController@calculateCostPercentage')->name('calculateCostPercentage');
        Route::post('calculateSalesPercentage', 'OurSurplusController@calculateSalesPercentage')->name('calculateSalesPercentage');
        Route::post('updatePrecies', 'OurSurplusController@updatePrecies')->name('updatePrecies');
        Route::post('updatePreciesSale', 'OurSurplusController@updatePreciesSale')->name('updatePreciesSale');
        Route::post('updateOrigin', 'OurSurplusController@updateOrigin')->name('updateOrigin');
        Route::get('getRegionSpecies', 'OurSurplusController@getRegionSpecies')->name('getRegionSpecies');
        Route::post('uploadPicture', 'OurSurplusController@upload_picture')->name('uploadPicture');
        Route::get('delete_file_catalog/{oursurplus_id}/{file_name}', 'OurSurplusController@delete_file_catalog')->name('delete_file_catalog');
    });
    Route::resource('our-surplus', 'OurSurplusController');

    Route::prefix('surplus')->name('surplus.')->group(function () {
        Route::post('delete_items', 'SurplusController@delete_items')->name('deleteItems');
        Route::post('upload_file', 'SurplusController@upload_file')->name('upload');
        Route::post('createOurSurplus', 'SurplusController@createOurSurplus')->name('createOurSurplus');
        Route::post('updateToMembersDate', 'SurplusController@updateToMembersDate')->name('updateToMembersDate');
        Route::post('selectSurplusList', 'SurplusController@selectSurplusList')->name('selectSurplusList');
        Route::post('saveSurplusList', 'SurplusController@saveSurplusList')->name('saveSurplusList');
        Route::post('deleteSurplusList', 'SurplusController@deleteSurplusList')->name('deleteSurplusList');
        Route::post('editSelectedRecords', 'SurplusController@editSelectedRecords')->name('editSelectedRecords');
        Route::get('filterSurplus', 'SurplusController@filterSurplus')->name('filterSurplus');
        Route::get('printSurplusList', 'SurplusController@printSurplusList')->name('printSurplusList');
        Route::get('askMoreSurplusDetails/{id}', 'SurplusController@askMoreSurplusDetails')->name('askMoreSurplusDetails');
        Route::get('surplusEmailToClients/{id}', 'SurplusController@surplusEmailToClients')->name('surplusEmailToClients');
        Route::post('sendSurplusEmail', 'SurplusController@sendSurplusEmail')->name('sendSurplusEmail');
        Route::get('delete_file/{surplus_id}/{file_name}', 'SurplusController@delete_file')->name('delete_file');
        Route::get('showAll', 'SurplusController@showAll')->name('showAll');
        Route::get('orderBy', 'SurplusController@orderBy')->name('orderBy');
        Route::get('removeFromSurplusSession/{key}', 'SurplusController@removeFromSurplusSession')->name('removeFromSurplusSession');
        Route::post('searchRelatedStandardSurplus', 'SurplusController@searchRelatedStandardSurplus')->name('searchRelatedStandardSurplus');
        Route::get('export', 'SurplusController@export')->name('export');
        Route::get('recordsPerPage', 'SurplusController@recordsPerPage')->name('recordsPerPage');
        Route::get('detailsSurplusSpecimens/{id}', 'SurplusController@detailsSurplusSpecimens')->name('detailsSurplusSpecimens');
        Route::post('sendSurplusDetailsSpecimens', 'SurplusController@sendSurplusDetailsSpecimens')->name('sendSurplusDetailsSpecimens');
        Route::post('updateDate', 'SurplusController@updateDate')->name('updateDate');
        Route::post('duplicateSurplus', 'SurplusController@duplicateSurplus')->name('duplicateSurplus');
        Route::get('resetListEmailNewSurplu', 'SurplusController@resetListEmailNewSurplu')->name('resetListEmailNewSurplu');
        Route::post('uploadPicture', 'SurplusController@upload_picture')->name('uploadPicture');
        Route::get('delete_file_catalog/{surplus_id}/{file_name}', 'SurplusController@delete_file_catalog')->name('delete_file_catalog');
    });
    Route::resource('surplus', 'SurplusController');

    Route::prefix('surplus-collection')->name('surplus-collection.')->group(function () {
        Route::post('delete_items', 'SurplusCollectionController@delete_items')->name('deleteItems');
        Route::post('upload_file', 'SurplusCollectionController@upload_file')->name('upload');
        Route::post('editSelectedRecords', 'SurplusCollectionController@editSelectedRecords')->name('editSelectedRecords');
        Route::get('filterSurplus', 'SurplusCollectionController@filterSurplus')->name('filterSurplus');
        Route::get('printSurplusList', 'SurplusCollectionController@printSurplusList')->name('printSurplusList');
        Route::get('askMoreSurplusDetails/{id}', 'SurplusCollectionController@askMoreSurplusDetails')->name('askMoreSurplusDetails');
        Route::post('sendSurplusEmail', 'SurplusCollectionController@sendSurplusEmail')->name('sendSurplusEmail');
        Route::get('delete_file/{surplus_id}/{file_name}', 'SurplusCollectionController@delete_file')->name('delete_file');
        Route::get('showAll', 'SurplusCollectionController@showAll')->name('showAll');
        Route::get('orderBy', 'SurplusCollectionController@orderBy')->name('orderBy');
        Route::get('removeFromSurplusSession/{key}', 'SurplusCollectionController@removeFromSurplusSession')->name('removeFromSurplusSession');
        Route::get('recordsPerPage', 'SurplusCollectionController@recordsPerPage')->name('recordsPerPage');
        Route::get('addressList', 'SurplusCollectionController@createSurplusCollectionAddressList')->name('createSurplusCollectionAddressList');
    });
    Route::resource('surplus-collection', 'SurplusCollectionController');

    Route::prefix('our-wanted')->name('our-wanted.')->group(function () {
        Route::post('delete_items', 'OurWantedController@delete_items')->name('deleteItems');
        Route::post('selectOurWantedList', 'OurWantedController@selectOurWantedList')->name('selectOurWantedList');
        Route::post('saveOurWantedList', 'OurWantedController@saveOurWantedList')->name('saveOurWantedList');
        Route::post('deleteOurWantedList', 'OurWantedController@deleteOurWantedList')->name('deleteOurWantedList');
        Route::post('editSelectedRecords', 'OurWantedController@editSelectedRecords')->name('editSelectedRecords');
        Route::get('filterOurWanted', 'OurWantedController@filterOurWanted')->name('filterOurWanted');
        Route::get('printOurWantedList', 'OurWantedController@printOurWantedList')->name('printOurWantedList');
        Route::get('export', 'OurWantedController@export')->name('export');
        Route::get('showAll', 'OurWantedController@showAll')->name('showAll');
        Route::get('orderBy', 'OurWantedController@orderBy')->name('orderBy');
        Route::get('removeFromOurWantedSession/{key}', 'OurWantedController@removeFromOurWantedSession')->name('removeFromOurWantedSession');
        Route::get('recordsPerPage', 'OurWantedController@recordsPerPage')->name('recordsPerPage');
        Route::post('checkSameRecord', 'OurWantedController@checkSameRecord')->name('checkSameRecord');
    });
    Route::resource('our-wanted', 'OurWantedController');

    Route::prefix('wanted')->name('wanted.')->group(function () {
        Route::post('delete_items', 'WantedController@delete_items')->name('deleteItems');
        Route::post('editSelectedRecords', 'WantedController@editSelectedRecords')->name('editSelectedRecords');
        Route::get('filterWanted', 'WantedController@filterWanted')->name('filterWanted');
        Route::get('export', 'WantedController@export')->name('export');
        Route::get('wantedEmailToSuppliers', 'WantedController@wantedEmailToSuppliers')->name('wantedEmailToSuppliers');
        Route::post('sendWantedEmail', 'WantedController@sendWantedEmail')->name('sendWantedEmail');
        Route::get('showAll', 'WantedController@showAll')->name('showAll');
        Route::get('orderBy', 'WantedController@orderBy')->name('orderBy');
        Route::get('removeFromWantedSession/{key}', 'WantedController@removeFromWantedSession')->name('removeFromWantedSession');
        Route::get('recordsPerPage', 'WantedController@recordsPerPage')->name('recordsPerPage');
        Route::get('resetListEmailNewWanted', 'WantedController@resetListEmailNewWanted')->name('resetListEmailNewWanted');
        Route::post('selectWantedList', 'WantedController@selectWantedList')->name('selectWantedList');
        Route::post('saveWantedList', 'WantedController@saveWantedList')->name('saveWantedList');
        Route::post('deleteWantedList', 'WantedController@deleteWantedList')->name('deleteWantedList');
        Route::get('printWantedList', 'WantedController@printWantedList')->name('printWantedList');
    });
    Route::resource('wanted', 'WantedController');

    Route::prefix('contacts')->name('contacts.')->group(function () {
        Route::post('delete_items', 'ContactController@delete_items')->name('deleteItems');
        Route::get('filterContacts', 'ContactController@filterContacts')->name('filterContacts');
        Route::get('checkForExistence', 'ContactController@checkForExistence')->name('checkForExistence');
        Route::get('doublesView', 'ContactController@doublesView')->name('doublesView');
        Route::get('filterDoubles', 'ContactController@filterDoubles')->name('filterDoubles');
        Route::get('compare/{contact}/{contact1}/{from}/{source_id}', 'ContactController@compare')->name('compare');
        Route::patch('merge', 'ContactController@merge')->name('merge');
        Route::post('editSelectedRecords', 'ContactController@editSelectedRecords')->name('editSelectedRecords');
        Route::get('export', 'ContactController@export')->name('export');
        Route::get('createContactAddressList', 'ContactController@createContactAddressList')->name('createContactAddressList');
        Route::get('showAll', 'ContactController@showAll')->name('showAll');
        Route::get('orderBy', 'ContactController@orderBy')->name('orderBy');
        Route::get('recordsPerPage', 'ContactController@recordsPerPage')->name('recordsPerPage');
        Route::get('removeFromContactSession/{key}', 'ContactController@removeFromContactSession')->name('removeFromContactSession');
        Route::get('restoreContactDeleted/{id}', 'ContactController@restoreContactDeleted')->name('restoreContactDeleted');
        Route::get('contactsSendEmail', 'ContactController@contactsSendEmail')->name('contactsSendEmail');
        Route::post('sendEmailTemplate', 'ContactController@sendEmailTemplate')->name('sendEmailTemplate');
        Route::get('resetListEmailNewContact', 'ContactController@resetListEmailNewContact')->name('resetListEmailNewContact');
    });
    Route::resource('contacts', 'ContactController');

    Route::prefix('contacts-approve')->name('contacts-approve.')->group(function () {
        Route::get('filterContactsToApprove', 'ContactToApproveController@filterContactsToApprove')->name('filterContactsToApprove');
        Route::post('sendApprovedStatusEmail', 'ContactToApproveController@sendApprovedStatusEmail')->name('sendApprovedStatusEmail');
        Route::get('showAll', 'ContactToApproveController@showAll')->name('showAll');
        Route::get('removeFromContactToApproveSession/{key}', 'ContactToApproveController@removeFromContactToApproveSession')->name('removeFromContactToApproveSession');
        Route::get('quickApprovalOption/{contact_id}/{option}', 'ContactToApproveController@quickApprovalOption')->name('quickApprovalOption');
        Route::post('quickSelectedApprovalOption', 'ContactToApproveController@quickSelectedApprovalOption')->name('quickSelectedApprovalOption');
    });
    Route::resource('contacts-approve', 'ContactToApproveController');

    Route::prefix('contacts-deleted')->name('contacts-deleted.')->group(function () {
        Route::get('filterContactsDeleted', 'ContactDeletedController@filterContactsDeleted')->name('filterContactsDeleted');
        Route::post('restore', 'ContactDeletedController@restore')->name('restore');
        Route::get('showAll', 'ContactDeletedController@showAll')->name('showAll');
        Route::get('removeFromContactDeletedSession/{key}', 'ContactDeletedController@removeFromContactDeletedSession')->name('removeFromContactDeletedSession');
    });

    Route::resource('contacts-deleted', 'ContactDeletedController');

    Route::prefix('organisations')->name('organisations.')->group(function () {
        Route::get('convert', 'OrganisationController@convertView')->name('convert-view');
        Route::post('convert', 'OrganisationController@convert')->name('convert');
        Route::post('convert-mass', 'OrganisationController@convertMass')->name('convert-mass');
        Route::get('/organisations-contacts-search', "OrganisationController@get")->name("search");
        Route::post('delete_items', 'OrganisationController@delete_items')->name('deleteItems');
        Route::get('filterOrganizations', 'OrganisationController@filterOrganizations')->name('filterOrganizations');
        Route::post('editSelectedRecords', 'OrganisationController@editSelectedRecords')->name('editSelectedRecords');
        Route::get('compare/{organisation}/{organisation1}/{from}/{source_id}', 'OrganisationController@compare')->name('compare');
        Route::post('mergeContact', 'OrganisationController@mergeContact')->name('mergeContact');
        Route::get('checkForExistence', 'OrganisationController@checkForExistence')->name('checkForExistence');
        Route::patch('merge', 'OrganisationController@merge')->name('merge');
        Route::get('doublesView', 'OrganisationController@doublesView')->name('doublesView');
        Route::get('filterOrganizationsDoubles', 'OrganisationController@filterOrganizationsDoubles')->name('filterOrganizationsDoubles');
        Route::get('searchDoubles', 'OrganisationController@searchDoubles')->name('searchDoubles');
        Route::get('createContact/{organisation}', 'OrganisationController@createContact')->name('createContact');
        Route::post('storeContact', 'OrganisationController@storeContact')->name('storeContact');
        Route::get('editContact/{contact}/{organisation}', 'OrganisationController@editContact')->name('editContact');
        Route::patch('updateContact/{contact}', 'OrganisationController@updateContact')->name('updateContact');
        Route::delete('destroyContact/{contact}/{organisation}', 'OrganisationController@destroyContact')->name('destroyContact');
        Route::get('showAll', 'OrganisationController@showAll')->name('showAll');
        Route::get('/organisations-contacts-search', 'OrganisationController@get')->name('search');
        Route::get('removeFromOrganizationSession/{key}', 'OrganisationController@removeFromOrganizationSession')->name('removeFromOrganizationSession');
        Route::get('export', 'OrganisationController@export')->name('export');
        Route::get('exportEmailInstitutionsLevelA', 'OrganisationController@exportEmailInstitutionsLevelA')->name('exportEmailInstitutionsLevelA');
        Route::get('recordsPerPage', 'OrganisationController@recordsPerPage')->name('recordsPerPage');
        Route::get('validateCanonical', 'OrganisationController@validateCanonical')->name('validateCanonical');
        Route::post('editLevel', 'OrganisationController@editLevel')->name('editLevel');
        Route::get('createOrganisationAddressList', 'OrganisationController@createOrganisationAddressList')->name('createOrganisationAddressList');
        Route::post('sendNewAnimals', 'OrganisationController@sendNewAnimals')->name('sendNewAnimals');
        Route::get('showNewAnimals', 'OrganisationController@showNewAnimals')->name('showNewAnimals');
        Route::post('institutionSendNewAnimal', 'OrganisationController@institutionSendNewAnimal')->name('institutionSendNewAnimal');
        Route::post('sendEmailValueNewAnimal', 'OrganisationController@sendEmailValueNewAnimal')->name('sendEmailValueNewAnimal');
        Route::post('deleteItemsNewAnimals', 'OrganisationController@deleteItemsNewAnimals')->name('deleteItemsNewAnimals');
        Route::get('resetListEmailNewOrganisation', 'OrganisationController@resetListEmailNewOrganisation')->name('resetListEmailNewOrganisation');
        Route::get('filterDateNewAnimals', 'OrganisationController@filterDateNewAnimals')->name('filterDateNewAnimals');
        Route::get('contactsForOrganisation/{organisation}', 'OrganisationController@getContactsForOrganisation')->name('getContactsForOrganisation');
    });
    Route::resource('organisations', 'OrganisationController');

    Route::prefix('classifications')->name('classifications.')->group(function () {
        Route::post('getOrdersByClass', 'ClassificationController@getOrdersByClass')->name('getOrdersByClass');
        Route::post('getFamiliesByOrder', 'ClassificationController@getFamiliesByOrder')->name('getFamiliesByOrder');
        Route::post('getGenusByFamily', 'ClassificationController@getGenusByFamily')->name('getGenusByFamily');
        Route::post('getGenusCode', 'ClassificationController@getGenusCode')->name('getGenusCode');
        Route::post('saveOrEditClass', 'ClassificationController@saveOrEditClass')->name('saveOrEditClass');
        Route::post('getClassificationById', 'ClassificationController@getClassificationById')->name('getClassificationById');
    });
    Route::resource('classifications', 'ClassificationController');

    Route::prefix('animals')->name('animals.')->group(function () {
        Route::get('filterAnimals', 'AnimalController@filterAnimals')->name('filterAnimals');
        Route::post('upload_picture', 'AnimalController@upload_picture')->name('uploadPicture');
        Route::post('delete_pictures', 'AnimalController@delete_pictures')->name('deletePictures');
        Route::post('delete_items', 'AnimalController@delete_items')->name('deleteItems');
        Route::get('export', 'AnimalController@export')->name('export');
        Route::post('assignCratesToSpecies', 'AnimalController@assignCratesToSpecies')->name('assignCratesToSpecies');
        Route::post('removeSpeciesCrates', 'AnimalController@removeSpeciesCrates')->name('removeSpeciesCrates');
        Route::post('assignCratesToSpeciesGroup', 'AnimalController@assignCratesToSpeciesGroup')->name('assignCratesToSpeciesGroup');
        Route::get('manage_classifications', 'AnimalController@manage_classifications')->name('manage_classifications');
        Route::get('orderBy', 'AnimalController@orderBy')->name('orderBy');
        Route::get('showAll', 'AnimalController@showAll')->name('showAll');
        Route::get('removeFromAnimalsSession/{key}', 'AnimalController@removeFromAnimalsSession')->name('removeFromAnimalsSession');
        Route::get('recordsPerPage', 'AnimalController@recordsPerPage')->name('recordsPerPage');
        Route::post('updateZootierListe', 'AnimalController@updateZootierListe')->name('updateZootierListe');
        Route::post('verifiCodeNumber', 'AnimalController@verifiCodeNumber')->name('verifiCodeNumber');
        Route::post('updateMainImage', 'AnimalController@updateMainImage')->name('updateMainImage');
    });
    Route::resource('animals', 'AnimalController');

    Route::resource('areas', 'AreaController');

    Route::post('regions/getCountriesByRegionId', 'RegionController@getCountriesByRegionId')->name('regions.getCountriesByRegionId');
    Route::resource('regions', 'RegionController');

    Route::prefix('origins')->name('origins.')->group(function () {
        Route::post('deleteItems', 'OriginController@delete_items')->name('deleteItems');
    });
    Route::resource('origins', 'OriginController');

    Route::prefix('countries')->name('countries.')->group(function () {
        Route::post('getCountriesByArea', 'CountryController@getCountriesByArea')->name('getCountriesByArea');
        Route::post('getAirportsByCountryId', 'CountryController@getAirportsByCountryId')->name('getAirportsByCountryId');
        Route::get('filter', 'CountryController@filter')->name('filter');
        Route::get('showAll', 'CountryController@showAll')->name('showAll');
        Route::get('removeFromCountrySession/{key}', 'CountryController@removeFromCountrySession')->name('removeFromCountrySession');
    });
    Route::resource('countries', 'CountryController');

    Route::prefix('crates')->name('crates.')->group(function () {
        Route::get('filterCrates', 'CrateController@filterCrates')->name('filterCrates');
        Route::post('delete_items', 'CrateController@delete_items')->name('deleteItems');
        Route::post('upload_file', 'CrateController@upload_file')->name('upload');
        Route::get('delete_file/{crate_id}/{file_name}', 'CrateController@delete_file')->name('delete_file');
        Route::get('export', 'CrateController@export')->name('export');
        Route::post('addSpeciesToCrate', 'CrateController@addSpeciesToCrate')->name('addSpeciesToCrate');
        Route::post('deleteSpeciesFromCrate', 'CrateController@deleteSpeciesFromCrate')->name('deleteSpeciesFromCrate');
        Route::post('getCratesByIata', 'CrateController@getCratesByIata')->name('getCratesByIata');
        Route::get('showAll', 'CrateController@showAll')->name('showAll');
        Route::get('orderBy', 'CrateController@orderBy')->name('orderBy');
        Route::get('removeFromCrateSession/{key}', 'CrateController@removeFromCrateSession')->name('removeFromCrateSession');
        Route::get('recordsPerPage', 'CrateController@recordsPerPage')->name('recordsPerPage');
    });
    Route::resource('crates', 'CrateController');

    Route::prefix('airfreights')->name('airfreights.')->group(function () {
        Route::get('filterAirfreights', 'AirfreightController@filterAirfreights')->name('filterAirfreights');
        Route::post('delete_items', 'AirfreightController@delete_items')->name('deleteItems');
        Route::post('upload_file', 'AirfreightController@upload_file')->name('upload');
        Route::post('getAirfreightById', 'AirfreightController@getAirfreightById')->name('getAirfreightById');
        Route::post('getAirfreightsByCountriesAndAirports', 'AirfreightController@getAirfreightsByCountriesAndAirports')->name('getAirfreightsByCountriesAndAirports');
        Route::get('delete_file/{airfreight_id}/{file_name}', 'AirfreightController@delete_file')->name('delete_file');
        Route::get('showAll', 'AirfreightController@showAll')->name('showAll');
        Route::get('removeFromAirfreightSession/{key}', 'AirfreightController@removeFromAirfreightSession')->name('removeFromAirfreightSession');
        Route::get('create/{offerOrSpeciesId?}/{offerAirfreightType?}', 'AirfreightController@create')->name('create');
        Route::get('recordsPerPage', 'AirfreightController@recordsPerPage')->name('recordsPerPage');
        Route::get('export', 'AirfreightController@export')->name('export');
        Route::post('updatePrecies', 'AirfreightController@updatePrecies')->name('updatePrecies');
    });
    Route::resource('airfreights', 'AirfreightController')->except(['create']);

    Route::get('airports/filter', 'AirportController@filter')->name('airports.filter');
    Route::get('airports/showAll', 'AirportController@showAll')->name('airports.showAll');
    Route::get('airports/removeFromAirportSession/{key}', 'AirportController@removeFromAirportSession')->name('airports.removeFromAirportSession');
    Route::resource('airports', 'AirportController');

    Route::post('users/delete_items', 'UserController@delete_items')->name('users.deleteItems');
    Route::resource('users', 'UserController');

    Route::resource('roles', 'RoleController');

    Route::get('permissions/filterPermissions', 'PermissionController@filterPermissions')->name('permissions.filterPermissions');
    Route::post('permissions/updateRolePermissions', 'PermissionController@updateRolePermissions')->name('permissions.updateRolePermissions');
    Route::resource('permissions', 'PermissionController');

    Route::prefix('offers')->name('offers.')->group(function () {
        Route::post('delete_items', 'OfferController@delete_items')->name('deleteItems');
        Route::post('delete_species', 'OfferController@delete_species')->name('deleteSpecies');
        Route::post('addOfferSpecies', 'OfferController@addOfferSpecies')->name('addOfferSpecies');
        Route::get('create_offer_pdf/{id}/{x_quantity?}/{parent_view?}', 'OfferController@create_offer_pdf')->name('create_offer_pdf');
        Route::get('create_offer_calculation_pdf/{id}/{parent_view?}', 'OfferController@create_offer_calculation_pdf')->name('create_offer_calculation_pdf');
        Route::post('export_offer_or_calculation_pdf', 'OfferController@export_offer_or_calculation_pdf')->name('export_offer_or_calculation_pdf');
        Route::post('upload_file', 'OfferController@upload_file')->name('upload');
        Route::get('delete_file/{offer_id}/{file_name}/{folder?}', 'OfferController@delete_file')->name('delete_file');
        Route::post('saveSpeciesValues', 'OfferController@saveSpeciesValues')->name('saveSpeciesValues');
        Route::post('updateOfferSpeciesCrateByCrateSelected', 'OfferController@updateOfferSpeciesCrateByCrateSelected')->name('updateOfferSpeciesCrateByCrateSelected');
        Route::post('saveSpeciesCrateValues', 'OfferController@saveSpeciesCrateValues')->name('saveSpeciesCrateValues');
        Route::post('saveOfferAirfreightType', 'OfferController@saveOfferAirfreightType')->name('saveOfferAirfreightType');
        Route::post('getOfferSpeciesAirfreights', 'OfferController@getOfferSpeciesAirfreights')->name('getOfferSpeciesAirfreights');
        Route::post('saveOfferSpeciesAirfreights', 'OfferController@saveOfferSpeciesAirfreights')->name('saveOfferSpeciesAirfreights');
        Route::post('removeOfferSpeciesAirfreight', 'OfferController@removeOfferSpeciesAirfreight')->name('removeOfferSpeciesAirfreight');
        Route::post('saveSelectedSpeciesAirfreightsValues', 'OfferController@saveSelectedSpeciesAirfreightsValues')->name('saveSelectedSpeciesAirfreightsValues');
        Route::post('saveSpeciesAirfreightVolKgRateValues', 'OfferController@saveSpeciesAirfreightVolKgRateValues')->name('saveSpeciesAirfreightVolKgRateValues');
        Route::post('saveOfferAirfreightPallet', 'OfferController@saveOfferAirfreightPallet')->name('saveOfferAirfreightPallet');
        Route::post('saveOfferAirfreightPalletValues', 'OfferController@saveOfferAirfreightPalletValues')->name('saveOfferAirfreightPalletValues');
        Route::post('saveOfferTransportTruck', 'OfferController@saveOfferTransportTruck')->name('saveOfferTransportTruck');
        Route::post('saveOfferTransportTruckValues', 'OfferController@saveOfferTransportTruckValues')->name('saveOfferTransportTruckValues');
        Route::post('saveAdditionalCostsValues', 'OfferController@saveAdditionalCostsValues')->name('saveAdditionalCostsValues');
        Route::post('addAdditionalCost', 'OfferController@addAdditionalCost')->name('addAdditionalCost');
        Route::delete('deleteAdditionalCost/{offer}/{id}', 'OfferController@deleteAdditionalCost')->name('deleteAdditionalCost');
        Route::get('filterOffers', 'OfferController@filterOffers')->name('filterOffers');
        Route::get('filterEmailsOffer', 'OfferController@filterEmailsOffer')->name('filterEmailsOffer');
        Route::get('export', 'OfferController@export')->name('export');
        Route::get('sendEmailOption/{id}/{email_code}/{parent_view?}/{is_action?}', 'OfferController@sendEmailOption')->name('sendEmailOption');
        Route::post('offerSendEmail', 'OfferController@offerSendEmail')->name('offerSendEmail');
        Route::get('orderBy', 'OfferController@orderBy')->name('orderBy');
        Route::get('showAll', 'OfferController@showAll')->name('showAll');
        Route::get('removeFromOfferSession/{key}', 'OfferController@removeFromOfferSession')->name('removeFromOfferSession');
        Route::get('offersWithStatus', 'OfferController@offersWithStatus')->name('offersWithStatus');
        Route::get('offersWithStatusLevel', 'OfferController@offersWithStatusLevel')->name('offersWithStatusLevel');
        Route::post('offerTask', 'OfferController@offerTask')->name('offerTask');
        Route::get('deleteOfferTask/{task_id}', 'OfferController@deleteOfferTask')->name('deleteOfferTask');
        Route::get('quickChangeStatus/{offer_id}/{status}', 'OfferController@quickChangeStatus')->name('quickChangeStatus');
        Route::post('checkAddingSpeciesRules', 'OfferController@checkAddingSpeciesRules')->name('checkAddingSpeciesRules');
        Route::post('editSelectedRecords', 'OfferController@editSelectedRecords')->name('editSelectedRecords');
        Route::post('selectedOffersAction', 'OfferController@selectedOffersAction')->name('selectedOffersAction');
        Route::post('selectedOfferTab', 'OfferController@selectedOfferTab')->name('selectedOfferTab');
        Route::post('addActionsToOffer', 'OfferController@addActionsToOffer')->name('addActionsToOffer');
        Route::post('editSelectedActions', 'OfferController@editSelectedActions')->name('editSelectedActions');
        Route::post('deleteSelectedActions', 'OfferController@deleteSelectedActions')->name('deleteSelectedActions');
        Route::post('uploadOfferActionDocument', 'OfferController@uploadOfferActionDocument')->name('uploadOfferActionDocument');
        Route::post('setExtraFee', 'OfferController@setExtraFee')->name('setExtraFee');
        Route::post('updateRemark', 'OfferController@updateRemark')->name('updateRemark');
        Route::post('setOriginRegion', 'OfferController@setOriginRegion')->name('setOriginRegion');
        Route::get('resetListEmailOfferSend', 'OfferController@resetListEmailOfferSend')->name('resetListEmailOfferSend');
        Route::post('resetValueCrateOffer', 'OfferController@resetValueCrateOffer')->name('resetValueCrateOffer');
        Route::post('getSpeciesValues', 'OfferController@getSpeciesValues')->name('getSpeciesValues');
        Route::post('getSpeciesCrateValues', 'OfferController@getSpeciesCrateValues')->name('getSpeciesCrateValues');
        Route::get('resetListEmailOfferInquiry', 'OfferController@resetListEmailOfferInquiry')->name('resetListEmailOfferInquiry');
        Route::get('quickChangeStatusLevelForapproval', 'OfferController@quickChangeStatusLevelForapproval')->name('quickChangeStatusLevelForapproval');
        Route::get('deleteCost', 'OfferController@deleteCost')->name('deleteCost');
        Route::post('getItemRelatedSurplus', 'OfferController@getItemRelatedSurplus')->name('getItemRelatedSurplus');
        Route::post('getSpeciesWithSameContinentAsOrigin', 'OfferController@getSpeciesWithSameContinentAsOrigin')->name('getSpeciesWithSameContinentAsOrigin');

    });
    Route::resource('offers', 'OfferController');

    Route::prefix('orders')->name('orders.')->group(function () {
        Route::post('delete_items', 'OrderController@delete_items')->name('deleteItems');
        Route::post('delete_species', 'OrderController@delete_species')->name('deleteSpecies');
        Route::post('addOrderSpecies', 'OrderController@addOfferSpecies')->name('addOrderSpecies');
        Route::post('upload_file', 'OrderController@upload_file')->name('upload');
        Route::get('delete_file/{offer_id}/{file_name}/{folder?}', 'OrderController@delete_file')->name('delete_file');
        Route::post('getBankAccountsByCompany', 'OrderController@getBankAccountsByCompany')->name('getBankAccountsByCompany');
        Route::post('create_invoice', 'OrderController@create_invoice')->name('create_invoice');
        Route::post('getInvoiceAmountBasedOnPercent', 'OrderController@getInvoiceAmountBasedOnPercent')->name('getInvoiceAmountBasedOnPercent');
        Route::post('getInvoicesBalancePercentLeft', 'OrderController@getInvoicesBalancePercentLeft')->name('getInvoicesBalancePercentLeft');
        Route::post('upload_invoice', 'OrderController@upload_invoice')->name('upload_invoice');
        Route::post('export_invoice_pdf', 'OrderController@export_invoice_pdf')->name('export_invoice_pdf');
        Route::get('create_order_documents_pdf/{order_id}/{doc_code}', 'OrderController@create_order_documents_pdf')->name('create_order_documents_pdf');
        Route::post('create_packing_list', 'OrderController@create_packing_list')->name('create_packing_list');
        Route::post('export_document_pdf', 'OrderController@export_document_pdf')->name('export_document_pdf');
        Route::post('editOrderInvoice', 'OrderController@editOrderInvoice')->name('editOrderInvoice');
        Route::post('setOrderInvoicePayment', 'OrderController@setOrderInvoicePayment')->name('setOrderInvoicePayment');
        Route::get('filterOrders', 'OrderController@filterOrders')->name('filterOrders');
        Route::get('filterEmailsOrder', 'OrderController@filterEmailsOrder')->name('filterEmailsOrder');
        Route::get('export', 'OrderController@export')->name('export');
        Route::get('orderBy', 'OrderController@orderBy')->name('orderBy');
        Route::get('showAll', 'OrderController@showAll')->name('showAll');
        Route::get('removeFromOrderSession/{key}', 'OrderController@removeFromOrderSession')->name('removeFromOrderSession');
        Route::get('ordersWithStatus', 'OrderController@ordersWithStatus')->name('ordersWithStatus');
        Route::post('orderTask', 'OrderController@orderTask')->name('orderTask');
        Route::get('deleteOrderTask/{task_id}', 'OrderController@deleteOrderTask')->name('deleteOrderTask');
        Route::get('sendEmailOption/{id}/{email_code}/{is_action?}', 'OrderController@sendEmailOption')->name('sendEmailOption');
        Route::get('sendClientInvoice/{order_id}/{invoice_id}', 'OrderController@sendClientInvoice')->name('sendClientInvoice');
        Route::post('orderSendEmail', 'OrderController@orderSendEmail')->name('orderSendEmail');
        Route::get('quickChangeOrder', 'OrderController@quickChangeOrder')->name('quickChangeOrder');
        Route::post('selectedOrderTab', 'OrderController@selectedOrderTab')->name('selectedOrderTab');
        Route::post('addActionsToOrder', 'OrderController@addActionsToOrder')->name('addActionsToOrder');
        Route::post('editSelectedActions', 'OrderController@editSelectedActions')->name('editSelectedActions');
        Route::post('deleteSelectedActions', 'OrderController@deleteSelectedActions')->name('deleteSelectedActions');
        Route::post('uploadOrderActionDocument', 'OrderController@uploadOrderActionDocument')->name('uploadOrderActionDocument');
        Route::post('updateRemark', 'OrderController@updateRemark')->name('updateRemark');
        Route::get('validateInvoiceNumber', 'OrderController@validateInvoiceNumber')->name('validateInvoiceNumber');
        Route::get('recordsPerPage', 'OrderController@recordsPerPage')->name('recordsPerPage');
        Route::post('editSelectedRecords', 'OrderController@editSelectedRecords')->name('editSelectedRecords');
        Route::get('resetListEmailNewOrderSend', 'OrderController@resetListEmailNewOrderSend')->name('resetListEmailNewOrderSend');
        Route::get('resetListEmailRealizedOrderSend', 'OrderController@resetListEmailRealizedOrderSend')->name('resetListEmailRealizedOrderSend');
    });
    Route::resource('orders', 'OrderController');

    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::post('ajaxGetInvoiceById', 'InvoiceController@ajaxGetInvoiceById')->name('ajaxGetInvoiceById');
        Route::get('filterInvoices', 'InvoiceController@filterInvoices')->name('filterInvoices');
        Route::get('export', 'InvoiceController@export')->name('export');
        Route::get('removeFromInvoiceSession/{key}', 'InvoiceController@removeFromInvoiceSession')->name('removeFromInvoiceSession');
        Route::get('showAll', 'InvoiceController@showAll')->name('showAll');
        Route::get('orderBy', 'InvoiceController@orderBy')->name('orderBy');
        Route::get('exportZip', 'InvoiceController@exportZip')->name('exportZip');
        Route::post('setInvoicePayment', 'InvoiceController@setInvoicePayment')->name('setInvoicePayment');
        Route::get('sendMultipleClientInvoice', 'InvoiceController@sendMultipleClientInvoice')->name('sendMultipleClientInvoice');
        Route::post('invoicesSendEmail', 'InvoiceController@invoicesSendEmail')->name('invoicesSendEmail');
    });
    Route::resource('invoices', 'InvoiceController');

    Route::prefix('interesting-websites')->name('interesting-websites.')->group(function () {
        Route::get('filter', 'InterestingWebsiteController@filter')->name('filter');
        Route::post('delete_items', 'InterestingWebsiteController@delete_items')->name('deleteItems');
        Route::get('showAll', 'InterestingWebsiteController@showAll')->name('showAll');
        Route::get('removeFromInterestingWebsiteSession/{key}', 'InterestingWebsiteController@removeFromInterestingWebsiteSession')->name('removeFromInterestingWebsiteSession');
    });
    Route::resource('interesting-websites', 'InterestingWebsiteController');

    Route::prefix('our-links')->name('our-links.')->group(function () {
        Route::get('filter', 'OurLinkController@filter')->name('filter');
        Route::post('delete_items', 'OurLinkController@delete_items')->name('deleteItems');
        Route::get('showAll', 'OurLinkController@showAll')->name('showAll');
        Route::get('removeFromOurLinkSession/{key}', 'OurLinkController@removeFromOurLinkSession')->name('removeFromOurLinkSession');
    });
    Route::resource('our-links', 'OurLinkController');

    Route::prefix('zoo-associations')->name('zoo-associations.')->group(function () {
        Route::get('filter', 'ZooAssociationController@filter')->name('filter');
        Route::post('delete_items', 'ZooAssociationController@delete_items')->name('deleteItems');
        Route::get('export', 'ZooAssociationController@export')->name('export');
        Route::get('showAll', 'ZooAssociationController@showAll')->name('showAll');
        Route::get('removeFromZooAssociationSession/{key}', 'ZooAssociationController@removeFromZooAssociationSession')->name('removeFromZooAssociationSession');
    });
    Route::resource('zoo-associations', 'ZooAssociationController');

    Route::prefix('codes')->name('codes.')->group(function () {
        Route::get('filter', 'CodeController@filter')->name('filter');
        Route::post('delete_items', 'CodeController@delete_items')->name('deleteItems');
        Route::get('showAll', 'CodeController@showAll')->name('showAll');
        Route::get('export', 'CodeController@export')->name('export');
        Route::get('removeFromCodeSession/{key}', 'CodeController@removeFromCodeSession')->name('removeFromCodeSession');
    });
    Route::resource('codes', 'CodeController');

    Route::prefix('guidelines')->name('guidelines.')->group(function () {
        Route::get('filter', 'GuidelineController@filter')->name('filter');
        Route::post('delete_items', 'GuidelineController@delete_items')->name('deleteItems');
        Route::get('showAll', 'GuidelineController@showAll')->name('showAll');
        Route::get('removeFromGuidelineSession/{key}', 'GuidelineController@removeFromGuidelineSession')->name('removeFromGuidelineSession');
    });
    Route::resource('guidelines', 'GuidelineController');

    Route::prefix('offers-reservations-contracts')->name('offers-reservations-contracts.')->group(function () {
        Route::get('filter', 'OfferReservationContractController@filter')->name('filter');
        Route::post('delete_items', 'OfferReservationContractController@delete_items')->name('deleteItems');
        Route::get('showAll', 'OfferReservationContractController@showAll')->name('showAll');
        Route::get('removeFromOfferReservationContractSession/{key}', 'OfferReservationContractController@removeFromOfferReservationContractSession')->name('removeFromOfferReservationContractSession');
    });
    Route::resource('offers-reservations-contracts', 'OfferReservationContractController');

    Route::prefix('protocols')->name('protocols.')->group(function () {
        Route::get('categoryProtocols', 'ProtocolController@categoryProtocols')->name('categoryProtocols');
        Route::get('filter', 'ProtocolController@filter')->name('filter');
        Route::post('delete_items', 'ProtocolController@delete_items')->name('deleteItems');
        Route::get('showAll', 'ProtocolController@showAll')->name('showAll');
        Route::get('removeFromProtocolSession/{key}', 'ProtocolController@removeFromProtocolSession')->name('removeFromProtocolSession');
    });
    Route::resource('protocols', 'ProtocolController');

    Route::prefix('std-texts')->name('std-texts.')->group(function () {
        Route::get('filter', 'StdTextController@filter')->name('filter');
        Route::post('delete_items', 'StdTextController@delete_items')->name('deleteItems');
        Route::get('showAll', 'StdTextController@showAll')->name('showAll');
        Route::get('removeFromStdTextSession/{key}', 'StdTextController@removeFromStdTextSession')->name('removeFromStdTextSession');
    });
    Route::resource('std-texts', 'StdTextController');

    Route::prefix('website-texts')->name('website-texts.')->group(function () {
        Route::get('filter', 'WebsiteTextPictureController@filter')->name('filter');
        Route::post('deleteWebsiteTexts', 'WebsiteTextPictureController@deleteWebsiteTexts')->name('deleteWebsiteTexts');
        Route::post('deleteWebsiteImages', 'WebsiteTextPictureController@deleteWebsiteImages')->name('deleteWebsiteImages');
        Route::get('showAll', 'WebsiteTextPictureController@showAll')->name('showAll');
        Route::get('removeFromWebsiteTextSession/{key}', 'WebsiteTextPictureController@removeFromWebsiteTextSession')->name('removeFromWebsiteTextSession');
        Route::post('selectedWebsiteTab', 'WebsiteTextPictureController@selectedWebsiteTab')->name('selectedWebsiteTab');
        Route::post('upload_file', 'WebsiteTextPictureController@upload_file')->name('upload_file');
    });
    Route::resource('website-texts', 'WebsiteTextPictureController');

    Route::get('bank_accounts/exportBankAccountInfo', 'BankAccountController@exportBankAccountInfo')->name('bank_accounts.exportBankAccountInfo');
    Route::resource('bank_accounts', 'BankAccountController');

    Route::prefix('tasks')->name('tasks.')->group(function () {
        Route::get('filter', 'TaskController@filter')->name('filter');
        Route::get('filterTodayTasks', 'TaskController@filterTodayTasks')->name('filterTodayTasks');
        Route::post('markSelectedTasksAsFinishedOrNot', 'TaskController@markSelectedTasksAsFinishedOrNot')->name('markSelectedTasksAsFinishedOrNot');
        Route::get('personal', 'TaskController@personal')->name('personal');
        Route::get('tasksForCalendar', 'TaskController@tasksForCalendar')->name('tasksForCalendar');
        Route::get('editCalendarTask', 'TaskController@editCalendarTask')->name('editCalendarTask');
        Route::get('indexCalendar', 'TaskController@indexCalendar')->name('indexCalendar');
        Route::get('createInCalendar', 'TaskController@createInCalendar')->name('createInCalendar');
        Route::post('dropAndDragInCanlendar', 'TaskController@dropAndDragInCanlendar')->name('dropAndDragInCanlendar');
        Route::get('showAll', 'TaskController@showAll')->name('showAll');
        Route::get('removeFromTaskSession/{key}', 'TaskController@removeFromTaskSession')->name('removeFromTaskSession');
        Route::get('showAllTodayTasks', 'TaskController@showAllTodayTasks')->name('showAllTodayTasks');
        Route::get('removeFromTodayTaskSession/{key}', 'TaskController@removeFromTodayTaskSession')->name('removeFromTodayTaskSession');
        Route::get('recordsPerPage', 'TaskController@recordsPerPage')->name('recordsPerPage');
        Route::post('delete_items', 'TaskController@delete_items')->name('deleteItems');
        Route::post('selectedTasksTab', 'TaskController@selectedTasksTab')->name('selectedTasksTab');
        Route::post('updateStatus', 'TaskController@updateStatus')->name('updateStatus');
        Route::post('resetListEmailTasksComplete', 'TaskController@resetListEmailTasksComplete')->name('resetListEmailTasksComplete');
    });
    Route::resource('tasks', 'TaskController');

    Route::get('/profile', 'ProfileController@index')->name('profile');
    Route::put('/profile', 'ProfileController@update')->name('profile.update');

    Route::get('/currencies', 'CurrencyController@index')->name('currencies.index');
    Route::get('/currencies/rates', 'CurrencyController@rates')->name('currencies.rates');

    Route::get('/veterinary_documents', 'VeterinaryDocumentController@index')->name('veterinary_documents.index');
    Route::post('/veterinary_documents/upload_file', 'VeterinaryDocumentController@upload_file')->name('veterinary_documents.upload_file');
    Route::post('/veterinary_documents/delete_files', 'VeterinaryDocumentController@delete_files')->name('veterinary_documents.delete_files');

    Route::get('/general_documents', 'GeneralDocumentController@index')->name('general_documents.index');
    Route::post('/general_documents/upload_file', 'GeneralDocumentController@upload_file')->name('general_documents.upload_file');
    Route::post('/general_documents/delete_files', 'GeneralDocumentController@delete_files')->name('general_documents.delete_files');
    Route::post('/general_documents/addDashboardDocument', 'GeneralDocumentController@addDashboardDocument')->name('general_documents.addDashboardDocument');

    Route::get('/logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

    Route::resource('basic-details', 'BasicDetailController');

    Route::prefix('mailings')->name('mailings.')->group(function () {
        Route::get('filter', 'MailingController@filter')->name('filter');
        Route::post('delete_items', 'MailingController@delete_items')->name('deleteItems');
        Route::get('showAll', 'MailingController@showAll')->name('showAll');
        Route::get('removeFromMailingSession/{key}', 'MailingController@removeFromMailingSession')->name('removeFromMailingSession');
    });
    Route::resource('mailings', 'MailingController');

    Route::prefix('domain-name-link')->name('domain-name-link.')->group(function () {
        Route::get('removeFromDomainNameSession/{key}', 'DomainNameLinkController@removeFromDomainNameSession')->name('removeFromDomainNameSession');
        Route::get('filter', 'DomainNameLinkController@filter')->name('filter');
        Route::get('showAll', 'DomainNameLinkController@showAll')->name('showAll');
        Route::post('delete_items', 'DomainNameLinkController@delete_items')->name('deleteItems');
    });

    Route::resource('domain-name-link', 'DomainNameLinkController');

    Route::prefix('search-mailings')->name('search-mailings.')->group(function () {
        Route::get('filter', 'SearchMailingController@filter')->name('filter');
        Route::post('delete_items', 'SearchMailingController@delete_items')->name('deleteItems');
        Route::get('showAll', 'SearchMailingController@showAll')->name('showAll');
        Route::get('removeFromSearchMailingSession/{key}', 'SearchMailingController@removeFromSearchMailingSession')->name('removeFromSearchMailingSession');
    });
    Route::resource('search-mailings', 'SearchMailingController');

    Route::get('/test-mail', function (Request $request) {
        Mail::to(auth()->user())->send(new TestMail());
    });

    Route::prefix('inbox')->name('inbox.')->group(function () {
        Route::get('getUpdateEmail', 'EmailsController@getUpdateEmail')->name('getUpdateEmail');
        Route::post('updateIsReaad', 'EmailsController@updateIsReaad')->name('updateIsReaad');
        Route::post('updateLabels', 'EmailsController@updateLabels')->name('updateLabels');
        Route::post('updateDirectory', 'EmailsController@updateDirectory')->name('updateDirectory');
        Route::post('deleteItems', 'EmailsController@delete_items')->name('deleteItems');
        Route::get('getSelectEmailCreate', 'EmailsController@getSelectEmailCreate')->name('getSelectEmailCreate');
        Route::get('getTokenGraph', 'EmailsController@getTokenGraph')->name('getTokenGraph');
        Route::get('addAccount', 'EmailsController@addAccount')->name('addAccount');
        Route::get('authGraph', 'EmailsController@authGraph')->name('authGraph');
        Route::post('assingOffer', 'EmailsController@assingOffer')->name('assingOffer');
        Route::post('getOfferAssing', 'EmailsController@getOfferAssing')->name('getOfferAssing');
        Route::post('getOrderAssing', 'EmailsController@getOrderAssing')->name('getOrderAssing');
        Route::post('assingOrder', 'EmailsController@assingOrder')->name('assingOrder');
        Route::post('getSurpluAssing', 'EmailsController@getSurpluAssing')->name('getSurpluAssing');
        Route::post('assingSurplu', 'EmailsController@assingSurplu')->name('assingSurplu');
        Route::post('getWantedAssing', 'EmailsController@getWantedAssing')->name('getWantedAssing');
        Route::post('assingWanted', 'EmailsController@assingWanted')->name('assingWanted');
        Route::post('archiveItems', 'EmailsController@archiveItems')->name('archiveItems');
        Route::post('archiveItems', 'EmailsController@archiveItems')->name('archiveItems');
        Route::post('storeTask', 'EmailsController@storeTask')->name('storeTask');
        Route::get('multipleContact', 'EmailsController@multipleContact')->name('multipleContact');
        Route::post('changeContact', 'EmailsController@changeContact')->name('changeContact');
        Route::post('getAllEmailAccount', 'EmailsController@getAllEmailAccount')->name('getAllEmailAccount');
        Route::post('getBodyEmail', 'EmailsController@getBodyEmail')->name('getBodyEmail');
        Route::post('updateEmailsChanges', 'EmailsController@updateEmailsChanges')->name('updateEmailsChanges');
        Route::post('sendEmail', 'EmailsController@sendEmail')->name('sendEmail');
        Route::post('replyEmail', 'EmailsController@replyEmail')->name('replyEmail');
        Route::post('forwardEmail', 'EmailsController@forwardEmail')->name('forwardEmail');
        Route::get('recordsPerPage', 'EmailsController@recordsPerPage')->name('recordsPerPage');
        Route::get('export', 'EmailsController@export')->name('export');
        Route::get('filterInbox', 'EmailsController@filterInbox')->name('filterInbox');
        Route::get('removeFromInboxSession/{key}', 'EmailsController@removeFromInboxSession')->name('removeFromInboxSession');
        Route::post('deleteAddressItems', 'EmailsController@delete_address_items')->name('deleteAddressItems');
        Route::get('downloadAttachment/{email_guid}/{attachment}/{to_email}', 'EmailsController@downloadAttachment')->name('downloadAttachment');
        Route::post('addDashboard', 'EmailsController@addDashboard')->name('addDashboard');
        Route::post('draftEmail', 'EmailsController@draftEmail')->name('draftEmail');
        Route::get('getDraftEmail', 'EmailsController@getDraftEmail')->name('getDraftEmail');
        Route::post('uploadAttachment', 'EmailsController@uploadAttachment')->name('uploadAttachment');
        Route::post('deleteAttachment', 'EmailsController@deleteAttachment')->name('deleteAttachment');
        Route::get('emailDashboard', 'EmailsController@emailDashboard')->name('emailDashboard');
        Route::post('forwardEmailBulk', 'EmailsController@forwardEmailBulk')->name('forwardEmailBulk');
        Route::post('updateSpam', 'EmailsController@updateSpam')->name('updateSpam');
        Route::post('attachmentEmailMime', 'EmailsController@attachmentEmailMime')->name('attachmentEmailMime');
        Route::post('addColorEmail', 'EmailsController@addColorEmail')->name('addColorEmail');
        Route::post('createColor', 'EmailsController@createColor')->name('createColor');
        Route::post('removeColor', 'EmailsController@removeColor')->name('removeColor');
        Route::post('deleteLabels', 'EmailsController@deleteLabels')->name('deleteLabels');
    });

    Route::resource('inbox', 'EmailsController');

    Route::prefix('default-text-task')->name('default-text-task.')->group(function () {
        Route::post('delete_items', 'DefaultTextTasksController@delete_items')->name('deleteItems');
    });

    Route::resource('default-text-task', 'DefaultTextTasksController');

    Route::prefix('labels')->name('labels.')->group(function () {
        Route::post('deleteItems', 'LabelsController@delete_items')->name('deleteItems');
    });
    Route::resource('labels', 'LabelsController');

    Route::prefix('directories')->name('directories.')->group(function () {
        Route::post('deleteItems', 'DirectoriesController@delete_items')->name('deleteItems');
    });
    Route::resource('directories', 'DirectoriesController');

    Route::prefix('dashboards')->name('dashboards.')->group(function () {
        Route::post('deleteItems', 'DashboardsController@delete_items')->name('deleteItems');
        Route::post('deleteDashboards', 'DashboardsController@delete_dashboard')->name('deleteDashboards');
        Route::get('getDashboardParent', 'DashboardsController@getDashboardParent')->name('getDashboardParent');
        Route::get('getItemBlock', 'DashboardsController@getItemBlock')->name('getItemBlock');
        Route::post('getFilterData', 'DashboardsController@getFilterData')->name('getFilterData');
    });
    Route::resource('dashboards', 'DashboardsController');

    Route::prefix('colors')->name('colors.')->group(function() {
        Route::post('deleteItems', 'ColorsController@delete_items')->name('deleteItems');
    });
    Route::resource('colors', 'ColorsController');
});
