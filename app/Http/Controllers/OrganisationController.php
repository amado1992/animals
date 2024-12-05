<?php
/**
 * PHP Version 7
 *
 * Organization controller
 *
 * @category Controllers
 * @package  IZS
 * @author   Majorlabel <info@majorlabel.nl>
 * @license  https://majorlabel.nl none.
 * @link     https://majorlabel.nl
 */

namespace App\Http\Controllers;

use App\Enums\ContactApprovedStatus;
use App\Enums\ContactMailingCategory;
use App\Enums\OrganisationInfoStatus;
use App\Enums\OrganisationLevel;
use App\Enums\OrganisationOrderByOptions;
use App\Enums\Specialty;
use App\Exports\InstitutionsAddressListExport;
use App\Exports\InstitutionsExport;
use App\Exports\InstitutionsExportLevelA;
use App\Http\Requests\ContactCreateRequest;
use App\Http\Requests\ContactUpdateRequest;
use App\Http\Requests\OrganisationCreateRequest;
use App\Http\Requests\OrganisationMergeRequest;
use App\Http\Requests\OrganisationUpdateRequest;
use App\Http\Resources\SearchInstituteContactResource;
use App\Models\AreaRegion;
use App\Models\Association;
use App\Models\Contact;
use App\Models\Country;
use App\Models\DomainNameLink;
use App\Models\Email;
use App\Models\InterestSection;
use App\Models\Labels;
use App\Models\Mailing;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Organisation;
use App\Models\OrganisationSendAnimalNew;
use App\Models\OrganisationType;
use App\Models\Region;
use App\Models\Surplus;
use App\Models\Wanted;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

/**
 * PHP Version 7
 *
 * Organization controller
 *
 * @category Controllers
 * @package  IZS
 * @author   Majorlabel <info@majorlabel.nl>
 * @license  https://majorlabel.nl none.
 * @link     https://majorlabel.nl
 */
class OrganisationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Renderable
     */
    public function index(): Renderable
    {
        $organisations = DB::table('organisations')
            ->selectRaw('id')
            ->selectRaw('country_id')
            ->selectRaw('relation_type')
            ->selectRaw('email')
            ->selectRaw('name')
            ->selectRaw('canonical_name')
            ->selectRaw('phone')
            ->selectRaw('domain_name')
            ->selectRaw('city')
            ->selectRaw('mailing_category')
            ->selectRaw('level')
            ->selectRaw('website')
            ->selectRaw('created_at')
            ->selectRaw('updated_at')
            ->selectRaw("'I' as model_type")
            ->selectRaw('organisation_types.label as type_label')
            ->selectRaw('organisation_types.key as type_key')
            ->selectRaw('null organisation_name')
            ->leftjoin(
                'organisation_types', 'organisation_types.key', '=',
                'organisations.organisation_type'
            );

        $contacts = DB::table('contacts')
            ->selectRaw('contacts.id as id')
            ->selectRaw('contacts.country_id as country_id')
            ->selectRaw('contacts.relation_type as relation_type')
            ->selectRaw('contacts.email as email')
            ->selectRaw('CONCAT(IFNULL(first_name, ""), " ", IFNULL(last_name, "")) as name')
            ->selectRaw("'--' as canonical_name")
            ->selectRaw('mobile_phone as phone')
            ->selectRaw('contacts.domain_name as domain_name')
            ->selectRaw('contacts.city as city')
            ->selectRaw('contacts.mailing_category as mailing_category')
            ->selectRaw('null as level')
            ->selectRaw('null as website')
            ->selectRaw('contacts.created_at as created_at')
            ->selectRaw('contacts.updated_at as updated_at')
            ->selectRaw("'C' as model_type")
            ->selectRaw('null as type_label')
            ->selectRaw('null as type_key')
            ->selectRaw("(select CONCAT(id, ';' ,name) from organisations where id = organisation_id) as organisation_name")
            ->where('deleted_at', '=', null);

        $types  = OrganisationType::orderBy('key')->pluck('label', 'key');
        $specificKeys = ['Z', 'PBF', 'AS', 'EDMAT'];
        $organization_types = [];
        foreach ($specificKeys as $key) {
            if (!empty($types[$key])) {
                $organization_types[$key] = $types[$key];
                unset($types[$key]);
            }
        }
        $organization_types["empty"] = "Empty";
        $organization_types = array_merge($organization_types, $types->toArray());
        $countries           = Country::orderBy('name')->pluck('name', 'id');
        $associationsEmail   = Association::orderBy('key')->pluck('label', 'key');
        $regions             = Region::orderBy('name')->pluck('name', 'id');
        $organization_levels = OrganisationLevel::get();
        $infoStatuses        = OrganisationInfoStatus::get();
        $associations        = Association::orderBy('key')->get();
        $areas               = AreaRegion::orderBy('name')->pluck('name', 'id');
        $mailing_categories  = ContactMailingCategory::get();

        $orderByOptions   = OrganisationOrderByOptions::get();
        $orderByDirection = null;
        $orderByField     = null;

        $filterData = [];
        $filterDataKeyVal = [];
        $filterIC = 'B';

        if (session()->has('organization.filter')) {
            $request = session('organization.filter');

            // Set filter Key Values for usage in filter modal, to set previously entered values
            $filterDataKeyVal['hidden_filter_model_type'] = 'B';
            foreach ($request as $key => $value) {
               if (substr($key, 0, 6) !== 'hidden' && $value !== 'all') {
                  if ($key !== 'orderByField' && $key !== 'orderByDirection') {
                     $filterDataKeyVal['hidden_' . $key] = $value;
                  }
               }
            }

            if (isset($request['filter_model_type'])) {
               $filterIC = $request['filter_model_type'];
               if ($filterIC === 'B') {
                  $txFilter = 'Institutions & Contacts';
               } elseif ($filterIC === 'I') {
                  $txFilter = 'Institutions';
               } else {
                  $txFilter = 'Contacts';
               }
               $filterData = Arr::add(
                  $filterData, 'filter_model_type',
                     $txFilter
               );
            }

            if ($filterIC === 'I' || $filterIC === 'B') {
               if (isset($request['filter_organisation_type'])) {
                   if ($request['filter_organisation_type'] === 'empty') {
                       $organisations->whereNull('organisation_types.key');
                   } else {
                       $organisations->where('organisation_types.key', $request['filter_organisation_type']);
                       $contacts->where('id', 0);
                   }

                   $filterData = Arr::add(
                       $filterData, 'filter_organisation_type',
                       'Type: ' . $request['filter_organisation_type']
                   );
               }
            }

            if (!isset($request['filter_name_empty'])) {
                if (isset($request['filter_name'])) {
                   if ($filterIC === 'B' || $filterIC === 'I') {
                      $organisations->where(
                         static function ($query) use ($request) {
                             $query->where('name', 'like', '%' . $request['filter_name'] . '%')
                                 ->orWhere('synonyms', 'like', '%' . strtolower($request['filter_name']) . '%');
                         }
                     );
                   }
                   if ($filterIC === 'B' || $filterIC === 'C') {
                      $contacts->where('first_name', 'like', '%' . $request['filter_name'] . '%')
                        ->orWhere('last_name', 'like', '%' . $request['filter_name'] . '%');

                   }
                   $filterData = Arr::add(
                       $filterData, 'filter_name',
                       'Name: ' . $request['filter_name']
                   );
                }
            } else {
                $filterData = Arr::add(
                    $filterData, 'filter_name_empty',
                    'Empty name: ' . $request['filter_name_empty']
                );
                if ($filterIC === 'B' || $filterIC === 'I') {
                   $organisations->whereNull('name');
                }
                if ($filterIC === 'B' || $filterIC === 'C') {
                  $contacts->whereNull('first_name')->whereNull('last_name');
                }
            }

            if ($filterIC === 'C') {
               if (!isset($request['filter_institution_name_empty'])) {
                  if (isset($request['filter_institution_name'])) {
                     $contacts->leftjoin(
                        'organisations', 'organisations.id', '=',
                        'contacts.organisation_id'
                     );
                     $contacts->where('organisations.name', 'like', '%' . $request['filter_institution_name'] . '%');
                     $filterData = Arr::add(
                        $filterData, 'filter_institution_name',
                           'Institution name: ' . $request['filter_institution_name']
                     );
                  }
               } else {
                  $filterData = Arr::add(
                     $filterData, 'filter_institution_name',
                        'Empty institution name: ' . $request['filter_institution_name_empty']
                  );
                  $contacts->whereNull('organisation_id');
               }
            }

            if ($filterIC === 'I' || $filterIC === 'B') {
               if (!isset($request['filter_canonical_name_empty'])) {
                   if (isset($request['filter_canonical_name'])) {
                       $organisations->where('canonical_name', 'like', '%' . $request['filter_canonical_name'] . '%');
                       $contacts->where('id', 0);

                       $filterData = Arr::add(
                           $filterData, 'filter_canonical_name',
                           'Canonical name: ' . $request['filter_canonical_name']
                       );
                   }
               } else {
                   $filterData = Arr::add(
                       $filterData, 'filter_canonical_name_empty',
                       'Empty canonical name: ' . $request['filter_canonical_name_empty']
                   );

                   $organisations->whereNull('canonical_name');
               }
            }

            if (!isset($request['filter_email_empty'])) {
                if (isset($request['filter_email'])) {
                   if ($filterIC === 'B' || $filterIC === 'I') {
                      $organisations->where('email', 'like', '%' . $request['filter_email'] . '%');
                   }
                   if ($filterIC === 'B' || $filterIC === 'C') {
                      $contacts->where('email', 'like', '%' . $request['filter_email'] . '%');
                   }

                    $filterData = Arr::add($filterData, 'filter_email', 'Email: ' . $request['filter_email']);
                }
            } else {
                $filterData = Arr::add(
                    $filterData, 'filter_email_empty',
                    'Empty email: ' . $request['filter_email_empty']
                );
                if ($filterIC === 'B' || $filterIC === 'I') {
                   $organisations->whereNull('email');
                }
                if ($filterIC === 'B' || $filterIC === 'C') {
                   $contacts->whereNull('email');
                }
            }

            if (!isset($request['filter_domain_name_empty'])) {
                if (isset($request['filter_domain_name'])) {
                   if ($filterIC === 'B' || $filterIC === 'I') {
                      $organisations->where('domain_name', 'like', '%' . $request['filter_domain_name'] . '%');
                   }
                   if ($filterIC === 'B' || $filterIC === 'C') {
                      $contacts->where('domain_name', 'like', '%' . $request['filter_domain_name'] . '%');
                   }

                    $filterData = Arr::add(
                        $filterData, 'filter_domain_name',
                        'Domain: ' . $request['filter_domain_name']
                    );
                }
            } else {
                $filterData = Arr::add(
                    $filterData, 'filter_domain_name_empty',
                    'Empty domain: ' . $request['filter_domain_name_empty']
                );

                if ($filterIC === 'B' || $filterIC === 'I') {
                   $organisations->whereNull('domain_name');
                }
                if ($filterIC === 'B' || $filterIC === 'C') {
                   $contacts->whereNull('domain_name');
                }
            }

            if (!isset($request['filter_phone_empty'])) {
                if (isset($request['filter_phone'])) {
                    $filterData = Arr::add($filterData, 'filter_phone', 'Phone: ' . $request['filter_phone']);

                    if ($filterIC === 'B' || $filterIC === 'I') {
                       $organisations->where('phone', 'like', '%' . $request['filter_phone'] . '%');
                    }
                    if ($filterIC === 'B' || $filterIC === 'C') {
                       $contacts->where('mobile_phone', 'like', '%' . $request['filter_phone'] . '%');
                    }
                }
            } else {
                $filterData = Arr::add(
                    $filterData, 'filter_phone_empty',
                    'Empty phone: ' . $request['filter_phone_empty']
                );

                if ($filterIC === 'B' || $filterIC === 'I') {
                   $organisations->whereNull('phone');
                }
                if ($filterIC === 'B' || $filterIC === 'C') {
                   $contacts->whereNull('mobile_phone');
                }
            }

            if (!isset($request['filter_city_empty'])) {
                if (isset($request['filter_city'])) {
                    if ($filterIC === 'B' || $filterIC === 'I') {
                       $organisations->where('city', 'like', '%' . $request['filter_city'] . '%');
                    }
                    if ($filterIC === 'B' || $filterIC === 'C') {
                       $contacts->where('city', 'like', '%' . $request['filter_city'] . '%');
                    }

                    $filterData = Arr::add($filterData, 'filter_city', 'City: ' . $request['filter_city']);
                }
            } else {
                $filterData = Arr::add(
                    $filterData, 'filter_city_empty',
                    'Empty city: ' . $request['filter_city_empty']
                );

                if ($filterIC === 'B' || $filterIC === 'I') {
                   $organisations->whereNull('city');
                }
                if ($filterIC === 'B' || $filterIC === 'C') {
                   $contacts->whereNull('city');
                }
            }

            if (isset($request['filter_country_id'])) {
                $filterCountry = Country::where('id', $request['filter_country_id'])->first();

                if ($request['filter_country_id'] == 0) {
                    if ($filterIC === 'B' || $filterIC === 'I') {
                       $organisations->whereNull('country_id');
                    }
                    if ($filterIC === 'B' || $filterIC === 'C') {
                       $contacts->whereNull('country_id');
                    }
                } else {
                    if ($filterIC === 'B' || $filterIC === 'I') {
                       $organisations->where('country_id', $filterCountry->id);
                    }
                    if ($filterIC === 'B' || $filterIC === 'C') {
                       $contacts->where('country_id', $filterCountry->id);
                    }
                }

                $filterData = Arr::add(
                    $filterData, 'filter_country_id',
                    'Country: ' . ($filterCountry != null ? $filterCountry->name : 'Empty')
                );
            }

            if (isset($request['filter_continent'])) {
                $filterRegion = Region::where('id', $request['filter_continent'])->first();

                if ($filterIC === 'B' || $filterIC === 'I') {
                   $organisations->whereRaw(
                      '(
                          select
                             region_id
                          from
                             countries
                          where
                             countries.id = organisations.country_id
                          limit
                             1
                        ) = ?', [$filterRegion->id]
                   );
                }

                if ($filterIC === 'B' || $filterIC === 'C') {
                   $contacts->whereRaw(
                      '(
                          select
                             region_id
                          from
                             countries
                          where
                             countries.id = contacts.country_id
                          limit
                             1
                       ) = ?', [$filterRegion->id]
                   );
                }
                $filterData = Arr::add($filterData, 'filter_continent', 'Region: ' . $filterRegion->name);
            }

            if ($filterIC === 'B' || $filterIC === 'I') {
               if (isset($request['filter_level'])) {
                   if ($request['filter_level'] === 'empty') {
                       $organisations->whereNull('level');
                   } else {
                       $organisations->where('level', $request['filter_level']);

                       $contacts->where(
                           static function ($query) use ($request) {
                               $query->whereNotNull('organisation_id')
                                   ->whereIn(
                                       'organisation_id',
                                       function ($subQuery) use ($request) {
                                           $subQuery->select('id')
                                               ->from('organisations')
                                               ->where('level', $request['filter_level']);
                                       }
                                   );
                           }
                       );
                   }

                   $filterData = Arr::add($filterData, 'filter_level', 'Level: ' . $request['filter_level']);
               }
            }

            if ($filterIC === 'B' || $filterIC === 'I') {
               if (isset($request['filter_has_website']) && $request['filter_has_website'] != 'all') {
                  $filterData = Arr::add(
                     $filterData, 'filter_has_website',
                     'Has website: ' . $request['filter_has_website']
                  );

                  if ($request['filter_has_website'] == 'yes') {
                     $organisations->where(
                        function ($query) {
                            $query->whereNotNull('website')
                                ->orWhereNotNull('facebook_page');
                        }
                     );
                  } else {
                     $organisations->where(
                        function ($query) {
                            $query->whereNull('website')
                                ->whereNull('facebook_page');
                        }
                    );
                }
            }

               if (isset($request['filter_website'])) {
                  $organisations->where('website', 'like', '%' . $request['filter_website'] . '%');

                $filterData = Arr::add($filterData, 'filter_website', 'Website: ' . $request['filter_website']);
            }

               if (!isset($request['filter_vat_empty'])) {
                  if (isset($request['filter_vat_number'])) {
                    $organisations->where('vat_number', 'like', '%' . $request['filter_vat_number'] . '%');

                    $filterData = Arr::add($filterData, 'filter_vat_number', 'VAT: ' . $request['filter_vat_number']);
                  }
               } else {
                  $filterData = Arr::add($filterData, 'filter_vat_empty', 'Empty vat: ' . $request['filter_vat_empty']);

                  $organisations->whereNull('vat_number');
               }

               if (isset($request['filter_remarks'])) {
                  $organisations->where('remarks', 'like', '%' . $request['filter_remarks'] . '%');
                  $filterData = Arr::add($filterData, 'filter_remarks', 'Remarks: ' . $request['filter_remarks']);
               }
            }

            if (isset($request['filter_association'])) {
                $filterAssociation = Association::where('key', $request['filter_association'])->first();

                if ($request['filter_association'] === 'empty') {
                   if ($filterIC === 'B' || $filterIC === 'I') {
                      $organisations->whereNotExists(
                         static function ($query) {
                            $query->select(DB::raw(1))
                                ->from('association_organisation')
                                ->whereRaw('organisations.id = association_organisation.organisation_id');
                         }
                      );
                   }

                   if ($filterIC === 'B' || $filterIC === 'C') {
                      $contacts->where(
                         static function ($query) {
                            $query->whereNull('organisation_id')
                                ->orWhereNotExists(
                                    static function ($query) {
                                        $query->select(DB::raw(1))
                                            ->from('association_organisation')
                                            ->whereRaw('contacts.organisation_id = association_organisation.organisation_id');
                                    }
                                );
                        }
                    );
                   }
                } else {
                   if ($filterIC === 'B' || $filterIC === 'I') {
                      $organisations->whereExists(
                         static function ($query) use ($filterAssociation) {
                            $query->select(DB::raw(1))
                               ->from('association_organisation')
                               ->whereRaw('organisations.id = association_organisation.organisation_id')
                               ->where('association_organisation.association_key', $filterAssociation->key);
                         }
                      );
                   }
                   if ($filterIC === 'B' || $filterIC === 'C') {
                      $contacts->whereExists(
                         static function ($query) use ($filterAssociation) {
                            $query->select(DB::raw(1))
                               ->from('association_organisation')
                               ->whereRaw('contacts.organisation_id = association_organisation.organisation_id')
                               ->where('association_organisation.association_key', $filterAssociation->key);
                         }
                      );
                   }
                }

                $filterData = Arr::add(
                    $filterData, 'filter_association',
                    'Association: ' . ($filterAssociation != null ? $filterAssociation->label : $request['filter_association'])
                );
            }

            if (isset($request['filter_has_surplus']) && $request['filter_has_surplus'] != 'all') {
                $filterData = Arr::add(
                    $filterData, 'filter_has_surplus',
                    'Has surplus: ' . $request['filter_has_surplus']
                );

                if ($request['filter_has_surplus'] == 'yes') {
                    if ($filterIC === 'B' || $filterIC === 'I') {
                       $organisations->whereExists(
                          static function ($query) {
                             $query->select(DB::raw(1))
                                ->from('surplus')
                                ->whereRaw('organisations.id = surplus.organisation_id');
                          }
                       );
                    }
                    if ($filterIC === 'B' || $filterIC === 'C') {
                       $contacts->whereExists(
                          static function ($query) {
                             $query->select(DB::raw(1))
                                ->from('surplus')
                                ->whereRaw('contacts.id = surplus.contact_id');
                        }
                    );
                    }
                } else {
                   if ($filterIC === 'B' || $filterIC === 'I') {
                      $organisations->whereNotExists(
                         static function ($query) {
                            $query->select(DB::raw(1))
                                ->from('surplus')
                                ->whereRaw('organisations.id = surplus.organisation_id');
                         }
                      );
                   }
                   if ($filterIC === 'B' || $filterIC === 'C') {
                      $contacts->whereNotExists(
                         static function ($query) {
                            $query->select(DB::raw(1))
                                ->from('surplus')
                                ->whereRaw('contacts.id = surplus.contact_id');
                        }
                    );
                   }
                }
            }

            if (isset($request['filter_has_wanted']) && $request['filter_has_wanted'] != 'all') {
                $filterData = Arr::add(
                    $filterData, 'filter_has_wanted',
                    'Has wanted: ' . $request['filter_has_wanted']
                );

                if ($request['filter_has_wanted'] == 'yes') {
                   if ($filterIC === 'B' || $filterIC === 'I') {
                      $organisations->whereExists(
                         static function ($query) {
                            $query->select(DB::raw(1))
                                ->from('wanted')
                                ->whereRaw('organisations.id = wanted.organisation_id');
                         }
                      );
                   }
                   if ($filterIC === 'B' || $filterIC === 'C') {
                      $contacts->whereExists(
                         static function ($query) {
                            $query->select(DB::raw(1))
                                ->from('wanted')
                                ->whereRaw('contacts.id = wanted.client_id');
                        }
                    );
                   }
                } else {
                   if ($filterIC === 'B' || $filterIC === 'I') {
                      $organisations->whereNotExists(
                         static function ($query) {
                            $query->select(DB::raw(1))
                                ->from('wanted')
                                ->whereRaw('organisations.id = wanted.organisation_id');
                         }
                      );
                   }
                   if ($filterIC === 'B' || $filterIC === 'C') {
                      $contacts->whereNotExists(
                         static function ($query) {
                            $query->select(DB::raw(1))
                                ->from('wanted')
                                ->whereRaw('contacts.id = wanted.client_id');
                        }
                    );
                   }
                }
            }

            if (isset($request['filter_has_requests']) && $request['filter_has_requests'] != 'all') {
                $filterData = Arr::add(
                    $filterData, 'filter_has_requests',
                    'Has requests: ' . $request['filter_has_requests']
                );

                if ($request['filter_has_requests'] == 'yes') {
                   if ($filterIC === 'B' || $filterIC === 'I') {
                      $organisations->whereExists(
                         function ($query) {
                            $query->select(DB::raw(1))
                                ->from('contacts')
                                ->join('offers', 'contacts.id', '=', 'offers.client_id')
                                ->whereRaw('contacts.organisation_id = organisations.id');
                         }
                      );
                   }
                   if ($filterIC === 'B' || $filterIC === 'C') {
                      $contacts->whereExists(
                         static function ($query) {
                            $query->select(DB::raw(1))
                                ->from('offers')
                                ->whereRaw('contacts.id = offers.client_id');
                        }
                    );
                   }
                } else {
                   if ($filterIC === 'B' || $filterIC === 'I') {
                      $organisations->whereNotExists(
                         function ($query) {
                            $query->select(DB::raw(1))
                                ->from('contacts')
                                ->join('offers', 'contacts.id', '=', 'offers.client_id')
                                ->whereRaw('contacts.organisation_id = organisations.id');
                         }
                      );
                   }
                   if ($filterIC === 'B' || $filterIC === 'C') {
                      $contacts->whereNotExists(
                         static function ($query) {
                            $query->select(DB::raw(1))
                                ->from('offers')
                                ->whereRaw('contacts.id = offers.client_id');
                        }
                    );
                   }
                }
            }

            if (isset($request['filter_has_orders']) && $request['filter_has_orders'] != 'all') {
               $filterData = Arr::add(
                  $filterData, 'filter_has_orders',
                    'Has orders: ' . $request['filter_has_orders']
                );

               if ($request['filter_has_orders'] == 'yes') {
                  if ($filterIC === 'B' || $filterIC === 'I') {
                     $organisations->whereExists(
                        function ($query) {
                            $query->select(DB::raw(1))
                                ->from('contacts')
                                ->join('orders', 'contacts.id', '=', 'orders.client_id')
                                ->whereRaw('contacts.organisation_id = organisations.id');
                        }
                    );
                  }
                  if ($filterIC === 'B' || $filterIC === 'C') {
                     $contacts->whereExists(
                         static function ($query) {
                            $query->select(DB::raw(1))
                                ->from('orders')
                                ->whereRaw('contacts.id = orders.client_id');
                        }
                    );
                  }
                } else {
                   if ($filterIC === 'B' || $filterIC === 'I') {
                      $organisations->whereNotExists(
                          function ($query) {
                             $query->select(DB::raw(1))
                                ->from('contacts')
                                ->join('orders', 'contacts.id', '=', 'orders.client_id')
                                ->whereRaw('contacts.organisation_id = organisations.id');
                          }
                      );
                   }
                   if ($filterIC === 'B' || $filterIC === 'C') {
                      $contacts->whereNotExists(
                         static function ($query) {
                            $query->select(DB::raw(1))
                                ->from('orders')
                                ->whereRaw('contacts.id = orders.client_id');
                        }
                    );
                   }
                }
            }

            if (isset($request['filter_has_invoices']) && $request['filter_has_invoices'] != 'all') {
                $filterData = Arr::add(
                    $filterData, 'filter_has_invoices',
                    'Has invoices: ' . $request['filter_has_invoices']
                );

                if ($request['filter_has_invoices'] == 'yes') {
                   if ($filterIC === 'B' || $filterIC === 'I') {
                      $organisations->whereExists(
                         function ($query) {
                            $query->select(DB::raw(1))
                                ->from('contacts')
                                ->join('invoices', 'contacts.id', '=', 'invoices.invoice_contact_id')
                                ->whereRaw('contacts.organisation_id = organisations.id');
                          }
                      );
                   }
                   if ($filterIC === 'B' || $filterIC === 'C') {
                      $contacts->whereExists(
                         static function ($query) {
                            $query->select(DB::raw(1))
                                ->from('invoices')
                                ->whereRaw('contacts.id = invoices.invoice_contact_id');
                        }
                    );
                   }
                } else {
                   if ($filterIC === 'B' || $filterIC === 'I') {
                      $organisations->whereNotExists(
                         function ($query) {
                            $query->select(DB::raw(1))
                                ->from('contacts')
                                ->join('invoices', 'contacts.id', '=', 'invoices.invoice_contact_id')
                                ->whereRaw('contacts.organisation_id = organisations.id');
                         }
                      );
                   }
                   if ($filterIC === 'B' || $filterIC === 'C') {
                      $contacts->whereNotExists(
                         static function ($query) {
                            $query->select(DB::raw(1))
                                ->from('invoices')
                                ->whereRaw('contacts.id = invoices.invoice_contact_id');
                        }
                    );
                   }
                }
            }

            if (isset($request['filter_has_collection']) && $request['filter_has_collection'] != 'all') {
                $filterData = Arr::add(
                    $filterData, 'filter_has_collection',
                    'Has collections: ' . $request['filter_has_collection']
                );

                if ($request['filter_has_collection'] == 'yes') {
                   if ($filterIC === 'B' || $filterIC === 'I') {
                      $organisations->whereExists(
                         static function ($query) {
                            $query->select(DB::raw(1))
                                ->from('surplus')
                                ->whereRaw('organisations.id = surplus.organisation_id')
                                ->where('surplus_status', '=', 'collection');
                         }
                      );
                   }
                   if ($filterIC === 'B' || $filterIC === 'C') {
                      $contacts->whereExists(
                         static function ($query) {
                            $query->select(DB::raw(1))
                                ->from('surplus')
                                ->whereRaw('contacts.id = surplus.organisation_id')
                                ->where('surplus_status', '=', 'collection');
                        }
                    );
                   }
                } else {
                   if ($filterIC === 'B' || $filterIC === 'I') {
                      $organisations->whereNotExists(
                         static function ($query) {
                            $query->select(DB::raw(1))
                                ->from('surplus')
                                ->whereRaw('organisations.id = surplus.organisation_id')
                                ->where('surplus_status', '=', 'collection');
                         }
                      );
                   }
                   if ($filterIC === 'B' || $filterIC === 'C') {
                      $contacts->whereNotExists(
                         static function ($query) {
                            $query->select(DB::raw(1))
                                ->from('surplus')
                                ->whereRaw('contacts.id = surplus.organisation_id')
                                ->where('surplus_status', '=', 'collection');
                        }
                    );
                   }
                }
            }

            if (isset($request['filter_mailing_category'])) {
                if ($request['filter_mailing_category'] === 'empty') {
                   if ($filterIC === 'B' || $filterIC === 'I') {
                      $organisations->whereNull('mailing_category');
                   }
                   if ($filterIC === 'B' || $filterIC === 'C') {
                      $contacts->whereNull('mailing_category');
                   }
                } else {
                   if ($filterIC === 'B' || $filterIC === 'I') {
                      $organisations->where('mailing_category', $request['filter_mailing_category']);
                   }
                   if ($filterIC === 'B' || $filterIC === 'C') {
                      $contacts->where('mailing_category', $request['filter_mailing_category']);
                   }
                }
                $value      = str_replace('_', ' ', $request['filter_mailing_category']);
                $filterData = Arr::add($filterData, 'filter_mailing_category', 'Mailing Category: ' . ucfirst($value));
            }

            if (isset($request['filter_relation_type']) && $request['filter_relation_type'] !== 'all') {
                $filterData = Arr::add(
                    $filterData, 'filter_relation_type',
                    'Relation type: ' . $request['filter_relation_type']
                );

                if ($request['filter_relation_type'] === 'both') {
                   if ($filterIC === 'B' || $filterIC === 'I') {
                      $organisations->where('relation_type', '=', 'both');
                   }
                   if ($filterIC === 'B' || $filterIC === 'C') {
                      $contacts->where('relation_type', '=', 'both');
                   }
                }

                if ($request['filter_relation_type'] === 'supplier') {
                   if ($filterIC === 'B' || $filterIC === 'I') {
                      $organisations->where('relation_type', '=', 'supplier');
                   }
                   if ($filterIC === 'B' || $filterIC === 'C') {

                   }$contacts->where('relation_type', '=', 'supplier');
                }

                if ($request['filter_relation_type'] === 'client') {
                   if ($filterIC === 'B' || $filterIC === 'I') {
                      $organisations->where('relation_type', '=', 'client');
                   }
                   if ($filterIC === 'B' || $filterIC === 'C') {
                      $contacts->where('relation_type', '=', 'client');
                   }
                }
            }
        }

        if ($filterIC === 'B') {
           $query = $organisations->union($contacts);
        } elseif ($filterIC === 'I') {
           $query = $organisations;
        } else {
           $query = $contacts;
        }

        if (isset($request['orderByDirection']) && isset($request['orderByField'])) {
            $orderByDirection = $request['orderByDirection'];
            $orderByField     = $request['orderByField'];

            if ($orderByDirection == 'desc') {
                $query->orderByDesc($orderByField);
            } else {
                $query->orderBy($orderByField);
            }
        } else {
            $query->orderByDesc('updated_at');
        }

        if (isset($request) && isset($request['recordsPerPage'])) {
            $organisations = $query->paginate($request['recordsPerPage']);
        } else {
            $organisations = $query->paginate(20);
        }
        
        // We need to know how much contacts there are to approve
        $nrContactsToApprove = Contact::NeedsApproval()->orderByDesc('updated_at')->get()->count();

        return view(
            'institutions.index', compact(
                'organisations',
                'organization_types',
                'countries',
                'associationsEmail',
                'regions',
                'organization_levels',
                'infoStatuses',
                'associations',
                'orderByOptions',
                'orderByDirection',
                'orderByField',
                'filterData',
                'filterDataKeyVal',
                'areas',
                'mailing_categories',
                'nrContactsToApprove',
            )
        );
    }

    /**
     * ConvertView
     *
     * @param Request $request The request
     *
     * @return Renderable
     */
    public function convertView(Request $request): Renderable
    {
        $request->validate(
            [
            'id'   => 'required',
            'type' => 'required|in:organisation,contact',
            ]
        );

        $id = $request->input('id');

        if ($request->input('type') === 'contact') {
            $model = Contact::find($id);
        } else {
            $model = Organisation::find($id);
        }

        $organisationTypes = OrganisationType::all()->pluck('label', 'key')->toArray();
        $countries         = Country::all()->pluck('name', 'id')->toArray();
        $organisations     = Organisation::all()->pluck('name', 'id')->toArray();

        return view(
            'institutions.convert', [
            'model'             => $model,
            'type'              => $request->input('type'),
            'organisationTypes' => $organisationTypes,
            'countries'         => $countries,
            'organisations'     => $organisations,
            ]
        );
    }

    /**
     * Convert
     *
     * @param Request $request The request.
     *
     * @return RedirectResponse
     */
    public function convert(Request $request): RedirectResponse
    {
        $id = $request->input('id');

        try {
            DB::beginTransaction();
            if ($request->input('type') === 'contact') {
                $model = Contact::find($id);

                $organisation = Organisation::create(
                    [
                    'name'              => $request->input('name'),
                    'relation_type'     => $request->input('relation_type'),
                    'specialty'         => $request->input('specialty'),
                    'domain_name'       => $request->input('domain_name'),
                    'organisation_type' => $request->input('organisation_type'),
                    'email'             => $request->input('email'),
                    'phone'             => $request->input('phone'),
                    'fax'               => $request->input('fax'),
                    'website'           => $request->input('website'),
                    'facebook_page'     => $request->input('facebook_page'),
                    'address'           => $request->input('address'),
                    'zipcode'           => $request->input('zipcode'),
                    'city'              => $request->input('city'),
                    'country_id'        => $request->input('country_id'),
                    'vat_number'        => $request->input('vat_number'),
                    'level'             => $request->input('level'),
                    'info_status'       => $request->input('info_status'),
                    'remarks'           => $request->input('remarks'),
                    'open_remarks'      => $request->input('open_remarks'),
                    'internal_remarks'  => $request->input('internal_remarks'),
                    'short_description' => $request->input('short_description'),
                    'mailing_category'  => $request->input('mailing_category'),
                    ]
                );

                if ($organisation) {
                    $model->interest_sections->each(
                        static function (InterestSection $section) use (
                            $organisation,
                            $model
                        ) {
                            DB::table('organisation_interestsections')->insert(
                                [
                                'organisation_id'      => $organisation->id,
                                'interest_section_key' => $section->key,
                                ]
                            );

                            DB::table('contact_interest_section')
                                ->where('contact_id', '=', $model->id)
                                ->where('interest_section_key', '=', $section->key)
                                ->delete();
                        }
                    );

                    $model->emails->each(
                        static function (Email $email) use ($organisation) {
                            $email->update(
                                [
                                'contact_id'      => null,
                                'organisation_id' => $organisation->id,
                                ]
                            );
                        }
                    );

                    DB::table('contacts')
                        ->where('owner_contact_id', '=', $model->id)
                        ->whereNull('deleted_at')
                        ->update(
                            [
                            'owner_contact_id' => null,
                            'organisation_id'  => $organisation->id,
                            ]
                        );

                    $model->surpluses->each(
                        static function (Surplus $surplus) use ($organisation) {
                            $surplus->update(
                                [
                                'contact_id'      => null,
                                'organisation_id' => $organisation->id,
                                ]
                            );
                        }
                    );

                    $model->offers->each(
                        static function (Offer $offer) use ($organisation) {
                            $offer->update(
                                [
                                'client_id'      => null,
                                'institution_id' => $organisation->id,
                                ]
                            );
                        }
                    );

                    $model->orders->each(
                        static function (Order $order) use ($organisation) {
                            $order->update(
                                [
                                'client_id'      => null,
                                'institution_id' => $organisation->id,
                                ]
                            );
                        }
                    );

                    $model->wanteds->each(
                        static function (Wanted $wanted) use ($organisation) {
                            $wanted->update(
                                [
                                'client_id'       => null,
                                'organisation_id' => $organisation->id,
                                ]
                            );
                        }
                    );

                    DB::table('invoices')
                        ->where('invoice_contact_id', '=', $model->id)
                        ->update(
                            [
                            'invoice_organisation_id' => $organisation->id,
                            'invoice_contact_id'      => null,
                            ]
                        );

                    $model->delete();
                } else {
                    throw new Exception('Something went wrong while converting record');
                }
            } else {
                $model = Organisation::find($id);

                $contact = Contact::create(
                    [
                    'first_name'             => $request->input('first_name'),
                    'last_name'              => $request->input('last_name'),
                    'relation_type'          => $request->input('relation_type'),
                    'specialty'              => $request->input('specialty'),
                    'title'                  => $request->input('title'),
                    'position'               => $request->input('position'),
                    'source'                 => $request->input('source'),
                    'member_approved_status' => $request->input('member_approved_status'),
                    'domain_name'            => $request->input('domain_name'),
                    'email'                  => $request->input('email'),
                    'mobile_phone'           => $request->input('phone'),
                    'city'                   => $request->input('city'),
                    'country_id'             => $request->input('country_id'),
                    'mailing_category'       => $request->input('mailing_category'),
                    ]
                );

                if ($contact) {
                    $model->interest->each(
                        static function ($section) use (
                            $contact,
                            $model
                        ) {
                            DB::table('contact_interest_section')->insert(
                                [
                                'organisation_id'      => $contact->id,
                                'interest_section_key' => $section->interest_section_key,
                                ]
                            );

                            DB::table('organisation_interestsections')
                                ->where('contact_id', '=', $model->id)
                                ->where('interest_section_key', '=', $section->interest_section_key)
                                ->delete();
                        }
                    );


                    $model->emails->each(
                        static function (Email $email) use ($contact) {
                            $email->update(
                                [
                                'contact_id'      => $contact->id,
                                'organisation_id' => null,
                                ]
                            );
                        }
                    );

                    DB::table('contacts')
                        ->where('organisation_id', '=', $model->id)
                        ->whereNull('deleted_at')
                        ->update(
                            [
                            'owner_contact_id' => $contact->id,
                            'organisation_id'  => null,
                            ]
                        );

                    $model->surpluses->each(
                        static function (Surplus $surplus) use ($contact) {
                            $surplus->update(
                                [
                                'contact_id'      => $contact->id,
                                'organisation_id' => null,
                                ]
                            );
                        }
                    );

                    $model->offers->each(
                        static function (Offer $offer) use ($contact) {
                            $offer->update(
                                [
                                'client_id'      => $contact->id,
                                'institution_id' => null,
                                ]
                            );
                        }
                    );

                    $model->orders->each(
                        static function (Order $order) use ($contact) {
                            $order->update(
                                [
                                'client_id'      => $contact->id,
                                ]
                            );
                        }
                    );

                    $model->wanteds->each(
                        static function (Wanted $wanted) use ($contact) {
                            $wanted->update(
                                [
                                'client_id'       => $contact->id,
                                'organisation_id' => null,
                                ]
                            );
                        }
                    );

                    DB::table('invoices')
                        ->where('invoice_organisation_id', '=', $model->id)
                        ->update(
                            [
                            'invoice_contact_id'      => $contact->id,
                            'invoice_organisation_id' => null,
                            ]
                        );

                    $model->delete();
                } else {
                    throw new Exception('Something went wrong while converting record');
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->back()->withErrors(
                [
                'msg' => $e->getMessage(),
                ]
            );
        }

        if ($request->input('type') === 'contact') {
            return redirect(route('organisations.show', ['organisation' => $organisation->id]));
        }

        return redirect(route('contacts.show', ['contact' => $contact->id]));
    }

    /**
     * Convertmass
     *
     * @param Request $request The request.
     *
     * @return JsonResponse
     */
    public function convertMass(Request $request): JsonResponse
    {
        $id = $request->input('id');

        try {
            DB::beginTransaction();

            if ($request->input('type') === 'contact') {
                $model = Contact::find($id)->load(
                    'surpluses',
                    'offers',
                    'orders',
                    'wanteds',
                    'emails',
                    'interest_sections',
                    'invoices',
                    'contacts'
                );

                $mapped = [
                    'name'              => optional($model)->first_name . " " . optional($model)->last_name,
                    'relation_type'     => optional($model)->relation_type,
                    'specialty'         => optional($model)->specialty,
                    'domain_name'       => $model->domain_name,
                    'organisation_type' => 'uk',
                    'email'             => optional($model)->email,
                    'phone'             => optional($model)->mobile_phone,
                    'website'           => optional($model)->domain_name,
                    'city'              => optional($model)->city,
                    'country_id'        => optional($model)->country_id,
                    'is_approved'       => 1,
                    'mailing_category'  => optional($model)->mailing_category,
                ];

                $organisation = Organisation::create($mapped);

                if ($organisation) {
                    $model->interest_sections->each(
                        static function (InterestSection $section) use (
                            $organisation,
                            $model
                        ) {
                            DB::table('organisation_interestsections')->insert(
                                [
                                'organisation_id'      => $organisation->id,
                                'interest_section_key' => $section->key,
                                ]
                            );

                            DB::table('contact_interest_section')
                                ->where('contact_id', '=', $model->id)
                                ->where('interest_section_key', '=', $section->key)
                                ->delete();
                        }
                    );

                    $model->emails->each(
                        static function (Email $email) use ($organisation) {
                            $email->update(
                                [
                                'contact_id'      => null,
                                'organisation_id' => $organisation->id,
                                ]
                            );
                        }
                    );

                    DB::table('contacts')
                        ->where('owner_contact_id', '=', $model->id)
                        ->whereNull('deleted_at')
                        ->update(
                            [
                            'owner_contact_id' => null,
                            'organisation_id'  => $organisation->id,
                            ]
                        );

                    $model->surpluses->each(
                        static function (Surplus $surplus) use ($organisation) {
                            $surplus->update(
                                [
                                'contact_id'      => null,
                                'organisation_id' => $organisation->id,
                                ]
                            );
                        }
                    );

                    $model->offers->each(
                        static function (Offer $offer) use ($organisation) {
                            $offer->update(
                                [
                                'client_id'      => null,
                                'institution_id' => $organisation->id,
                                ]
                            );
                        }
                    );

                    $model->orders->each(
                        static function (Order $order) use ($organisation) {
                            $order->update(
                                [
                                'client_id'      => null,
                                'institution_id' => $organisation->id,
                                ]
                            );
                        }
                    );

                    $model->wanteds->each(
                        static function (Wanted $wanted) use ($organisation) {
                            $wanted->update(
                                [
                                'client_id'       => null,
                                'organisation_id' => $organisation->id,
                                ]
                            );
                        }
                    );

                    DB::table('invoices')
                        ->where('invoice_contact_id', '=', $model->id)
                        ->update(
                            [
                            'invoice_organisation_id' => $organisation->id,
                            'invoice_contact_id'      => null,
                            ]
                        );

                    $model->delete();
                } else {
                    throw new Exception('Something went wrong while converting record');
                }
            } else {
                $model = Organisation::find($id)->load(
                    'surpluses',
                    'offers',
                    'orders',
                    'wanteds',
                    'emails',
                    'interest',
                    'invoices',
                    'contacts'
                );

                $mapped = [
                    'first_name'       => optional($model)->name,
                    'relation_type'    => optional($model)->relation_type,
                    'specialty'        => optional($model)->specialty,
                    'domain_name'      => optional($model)->domain_name,
                    'email'            => optional($model)->email,
                    'mobile_phone'     => optional($model)->phone,
                    'city'             => optional($model)->city,
                    'country_id'       => optional($model)->country_id,
                    'mailing_category' => optional($model)->mailing_category,
                ];

                $contact = Contact::create($mapped);

                if ($contact) {
                    $model->interest->each(
                        static function ($section) use (
                            $contact,
                            $model
                        ) {
                            DB::table('contact_interest_section')->insert(
                                [
                                'organisation_id'      => $contact->id,
                                'interest_section_key' => $section->interest_section_key,
                                ]
                            );

                            DB::table('organisation_interestsections')
                                ->where('contact_id', '=', $model->id)
                                ->where('interest_section_key', '=', $section->interest_section_key)
                                ->delete();
                        }
                    );


                    $model->emails->each(
                        static function (Email $email) use ($contact) {
                            $email->update(
                                [
                                'contact_id'      => $contact->id,
                                'organisation_id' => null,
                                ]
                            );
                        }
                    );

                    DB::table('contacts')
                        ->where('organisation_id', '=', $model->id)
                        ->whereNull('deleted_at')
                        ->update(
                            [
                            'owner_contact_id' => $contact->id,
                            'organisation_id'  => null,
                            ]
                        );

                    $model->surpluses->each(
                        static function (Surplus $surplus) use ($contact) {
                            $surplus->update(
                                [
                                'contact_id'      => $contact->id,
                                'organisation_id' => null,
                                ]
                            );
                        }
                    );

                    $model->offers->each(
                        static function (Offer $offer) use ($contact) {
                            $offer->update(
                                [
                                'client_id'      => $contact->id,
                                'institution_id' => null,
                                ]
                            );
                        }
                    );

                    $model->orders->each(
                        static function (Order $order) use ($contact) {
                            $order->update(
                                [
                                'client_id'      => $contact->id,
                                'institution_id' => null,
                                ]
                            );
                        }
                    );

                    $model->wanteds->each(
                        static function (Wanted $wanted) use ($contact) {
                            $wanted->update(
                                [
                                'client_id'       => $contact->id,
                                'organisation_id' => null,
                                ]
                            );
                        }
                    );

                    DB::table('invoices')
                        ->where('invoice_organisation_id', '=', $model->id)
                        ->update(
                            [
                            'invoice_contact_id'      => $contact->id,
                            'invoice_organisation_id' => null,
                            ]
                        );

                    $model->delete();
                } else {
                    throw new Exception('Something went wrong while converting record');
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(
                [
                'status' => false,
                'msg'    => $e->getMessage(),
                ]
            );
        }

        return response()->json(
            [
            'status' => true,
            ]
        );
    }

    /**
     * CreateOrganisationAddressList
     *
     * @param Request $request The request
     *
     * @return string
     */
    public function createOrganisationAddressList(Request $request)
    {
        $file_name = 'Institutions address list ' . Carbon::now()->format('Y-m-d') . '.csv';

        //DB::enableQueryLog();
        $institutions = Organisation::where('mailing_category', 'all_mailings');

        $institutions->where(
            function ($query) {
                $query->whereNotNull('email')
                    ->where(DB::raw('TRIM(email)'), '<>', '');
            }
        );

        if (!empty($request->exclude_associations)) {
            $institutions->leftJoin('association_organisation as ao', 'organisations.id', '=', 'ao.organisation_id');
            $ea = explode(',', $request->exclude_associations);
            $institutions->where(
                function ($query) use ($ea) {
                    $query->whereNull('ao.association_key')
                        ->orwhereNotIn('ao.association_key', $ea);
                }
            );
        }

        $newMailing               = new Mailing();
        $newMailing->date_created = Carbon::now()->format('Y-m-d H:i:s');

        if (isset($request->level)) {
            $newMailing->institution_level = $request->level;
            $institutions->where('level', $request->level);
        }

        if (isset($request->itypes)) {
            $newMailing->institution_types = $request->itypes;

            $institutions->whereIn('organisation_type', explode(',', $request->itypes));
        }

        $newMailing->language = $request->language;
        if (isset($request->language) && $request->language != 'all') {
            $institutions->whereHas(
                'country', function ($query) use ($request) {
                    $query->where('language', $request->language);
                }
            );
        }

        $exclude_continents = [];
        if (isset($request->exclude_continents)) {
            $exclude_continents = explode(',', $request->exclude_continents);

            $exclude_continents_names       = Region::whereIn('id', $exclude_continents)->pluck('name')->all();
            $newMailing->exclude_continents = implode(', ', $exclude_continents_names);
        }

        //$exclude_countries = [];
        if (isset($request->exclude_countries)) {
            $exclude_countries = explode(',', $request->exclude_countries);

            $exclude_countries_names       = Country::select('name')->whereIn(
                'id',
                $exclude_countries
            )->pluck('name')->all();
            $newMailing->exclude_countries = implode(', ', $exclude_countries_names);
        } else {
            $exclude_countries = [];
        }

        if (isset($request->world_region)) {
            $world_regions = explode(',', $request->world_region_selection);

            if (in_array('0', $world_regions)) {
                $world_regions_name = AreaRegion::select('name')->pluck('name')->all();
            } else {
                $world_regions_name = AreaRegion::select('name')->whereIn('id', $world_regions)->pluck('name')->all();
            }
            $newMailing->part_of_world = implode(', ', $world_regions_name);

            switch ($request->world_region) {
            case 'country':
                if (!in_array('0', $world_regions)) {
                    $institutions->whereHas(
                        'country', function ($query) use ($world_regions) {
                                $query->whereIn('id', $world_regions);
                        }
                    );
                }
                break;

            case 'region':
                if (!in_array('0', $world_regions)) {
                    $institutions->whereHas(
                        'country', function ($query) use ($world_regions, $exclude_countries) {
                                $query->whereIn('region_id', $world_regions)
                                    ->when(
                                        (count($exclude_countries) > 0), function ($query) use ($exclude_countries) {
                                            return $query->whereNotIn('id', $exclude_countries);
                                        }
                                    );
                        }
                    );
                }
                break;
            default:
                if (!in_array('0', $world_regions)) {
                    $institutions->whereHas(
                        'country',
                        function ($query) use ($world_regions, $exclude_continents, $exclude_countries) {
                            $query->whereHas(
                                'region', function ($query) use ($world_regions) {
                                        $query->whereIn('area_region_id', $world_regions);
                                }
                            )
                                ->when(
                                    (count($exclude_continents) > 0),
                                    function ($query) use ($exclude_continents) {
                                            return $query->whereNotIn('region_id', $exclude_continents);
                                    }
                                )
                                ->when(
                                    (count($exclude_countries) > 0),
                                    function ($query) use ($exclude_countries) {
                                            return $query->whereNotIn('id', $exclude_countries);
                                    }
                                );
                        }
                    );
                }
                break;
            }
        }
        $institutions->selectRaw("*, "
              . "(select label from organisation_types where organisation_types.key = organisations.organisation_type) as organisation_type, "
              . "(select name from countries where id = country_id) as country, "
              . "(select name from regions where id IN (select region_id from countries where id = country_id)) as region");

        $institutions = $institutions->orderBy('organisations.id')->get();
        if ($institutions->count() > 0) {
            //dump(DB::getQueryLog());

            $newMailing->save();
            $export = new InstitutionsAddressListExport($institutions);

            return Excel::download($export, $file_name);
        } else {
            return redirect(route('organisations.index'))->with(
                'error',
                'No institution associated with this filter was found'
            );
        }
    }

    /**
     * Show all.
     *
     * @return \Illuminate\Routing\Redirector
     */
    public function showAll()
    {
        session()->forget('organization.filter');

        return redirect(route('organisations.index'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request The request.
     *
     * @return Renderable
     */
    public function create(Request $request): Renderable
    {
        $organization_types  = OrganisationType::orderBy('key')->pluck('label', 'key');
        $countries           = Country::orderBy('name')->pluck('name', 'id');
        $organization_levels = OrganisationLevel::get();
        $infoStatuses        = OrganisationInfoStatus::get();
        $interest_sections   = InterestSection::orderBy('key', 'desc')->get();
        $associations        = Association::orderBy('key')->get();
        $mailing_categories  = ContactMailingCategory::get();
        $specialties         = Specialty::get();

        $params = compact(
            'organization_types',
            'countries',
            'organization_levels',
            'infoStatuses',
            'interest_sections',
            'associations',
            'mailing_categories',
            'specialties'
        );

        if ($request->filled('preset')) {
            $params["name"]       = $request->input('name');
            $params["domain"]     = $request->input('domain');
            $params["city"]       = $request->input('city');
            $params["country_id"] = $request->input('country_id');
        }

        return view('institutions.create', $params);
    }

    /**
     * Search for
     *
     * @param Request $request The request
     *
     * @return AnonymousResourceCollection
     */
    public function get(Request $request): AnonymousResourceCollection
    {
        $request->validate(
            [
            'query' => 'required|min:3',
            ]
        );

        $query = $request->input('query');

        $organisations = Organisation::where('name', 'like', "%{$query}%")
            ->orWhere("email", "like", "%{$query}%")
            ->orWhere('id', $query)
            ->get();

        $contacts = Contact::where('first_name', 'like', "%{$query}%")
            ->orWhere('last_name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->orWhere('id', $query)
            ->get();

        $results = collect([...$organisations, ...$contacts]);

        return SearchInstituteContactResource::collection($results);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\OrganisationCreateRequest $request the request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(OrganisationCreateRequest $request)
    {
        $domain_name = ($request->email != null) ? substr($request->email, strpos($request->email, '@') + 1) : null;

        if ($request->domain_name == null) {
            $request['domain_name'] = $domain_name;
        }

        if ($request->filled('synonyms')) {
            $request->merge(['synonyms' => strtolower($request->input('synonyms'))]);
        }

        $request['new_organisation'] = true;

        $organisation = Organisation::create($request->all());
        $organisation->interest()->sync($request->interest_section);
        $organisation->associations()->sync($request->associations);

        $contact = Contact::where('email', $request->email)->first();

        if (!empty($request->contact_first_name) && !empty($request->contact_last_name)) {
            if ($contact === null) {
                $newContact                  = new Contact();
                $newContact->specialty       = $request->specialty;
                $newContact->relation_type   = $request->relation_type;
                $newContact->email           = $request->email;
                $newContact->domain_name     = $organisation->domain_name;
                $newContact->first_name      = $request->contact_first_name;
                $newContact->last_name       = $request->contact_last_name;
                $newContact->country_id      = $request->country_id;
                $newContact->city            = $request->city;
                $newContact->mobile_phone    = $request->phone;
                $newContact->organisation_id = $organisation->id;
                $newContact->source          = 'crm';
                $newContact->new_contact     = 1;
                $newContact->save();

                if (!empty($request->items_email_institution)) {
                    $label       = Labels::where('name', 'new_contact')->first();
                    $email_inbox = Email::where('from_email', $request->email)->get();
                    foreach ($email_inbox as $row) {
                        $row['contact_id'] = $newContact->id;
                        $row->labels()->detach($label);
                        $row->save();
                    }
                }

                $newContact->interest_sections()->sync($request->interest_section);
            } else {
                $contact->domain_name = $organisation->domain_name;
                if ($contact->first_name === null) {
                    $contact->first_name = $request->name;
                }
                if ($contact->country_id === null) {
                    $contact->country_id = $request->country_id;
                }
                if ($contact->city === null) {
                    $contact->city = $request->city;
                }
                if ($contact->mobile_phone === null) {
                    $contact->mobile_phone = $request->phone;
                }
                $contact->organisation_id = $organisation->id;
                if ($contact->source === null) {
                    $contact->source = 'crm';
                }
                $contact->update();

                $contact->interest_sections()->sync($request->interest_section);
            }
        }

        $label       = Labels::where('name', 'new_contact')->first();
        $email_inbox = Email::where('from_email', $request->email)->get();
        foreach ($email_inbox as $row) {
            $row['organisation_id'] = $organisation->id;
            if (!empty($newContact)) {
                $row['contact_id'] = $newContact->id;
            }
            $row->labels()->detach($label);
            $row->save();
        }

        return redirect(route('organisations.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Organisation $organisation the request.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Organisation $organisation)
    {
        $related_organizations = [];
        $related_organizations = Organisation::where('id', '<>', $organisation->id)
                                            ->where(
                                                function ($query) use ($organisation) {
                                                    $query->where('name', $organisation->name)
                                                        ->orWhere('domain_name', $organisation->domain_name);
                                                }
                                            )
                                             ->get();

        $user = Auth::user();
        if (!empty($user->id) && $user->id === 2) {
            $organisation['new_organisation'] = 0;
            $organisation->save();
        }

        $organizationSurpluses      = [];
        $organizationWanteds        = [];
        $organizationPendingOffers  = [];
        $organizationPendingOrders  = [];
        $organizationRealizedOrders = [];
        $mailing_categories         = ContactMailingCategory::get();

        if ($organisation->surpluses->count() > 0) {
            $organizationSurpluses = Arr::collapse([$organizationSurpluses, $organisation->surpluses]);
        }

        if ($organisation->wanteds->count() > 0) {
            $organizationWanteds = Arr::collapse([$organizationWanteds, $organisation->wanteds]);
        }

        foreach ($organisation->contacts as $contact) {
            $contactPendingOffers = $contact->offers()->where('offer_status', 'Pending')->get();
            if ($contactPendingOffers->count() > 0) {
                $organizationPendingOffers = Arr::collapse([$organizationPendingOffers, $contactPendingOffers]);
            }

            $contactPendingOrders = $contact->orders_contact_client()->where('order_status', 'Pending')->get();
            if ($contactPendingOrders->count() > 0) {
                $organizationPendingOrders = Arr::collapse([$organizationPendingOrders, $contactPendingOrders]);
            }

            $contactRealizedOrders = $contact->orders_contact_client()->where('order_status', 'Realized')->get();
            if ($contactRealizedOrders->count() > 0) {
                $organizationRealizedOrders = Arr::collapse([$organizationRealizedOrders, $contactRealizedOrders]);
            }
        }

        $perPage = 8;

        $currentSurplusPage       = Paginator::resolveCurrentPage('surpluses');
        $currentWantedPage        = Paginator::resolveCurrentPage('wanteds');
        $currentPendingOfferPage  = Paginator::resolveCurrentPage('pending_offers');
        $currentPendingOrderPage  = Paginator::resolveCurrentPage('pending_orders');
        $currentRealizedOrderPage = Paginator::resolveCurrentPage('realized_orders');

        $currentSurpluses      = array_slice($organizationSurpluses, $perPage * ($currentSurplusPage - 1), $perPage);
        $organizationSurpluses = new Paginator(
            $currentSurpluses, count($organizationSurpluses), $perPage,
            $currentSurplusPage, ['pageName' => 'surpluses', 'path' => Paginator::resolveCurrentPath()]
        );

        $currentWanteds      = array_slice($organizationWanteds, $perPage * ($currentWantedPage - 1), $perPage);
        $organizationWanteds = new Paginator(
            $currentWanteds, count($organizationSurpluses), $perPage,
            $currentWantedPage, ['pageName' => 'wanteds', 'path' => Paginator::resolveCurrentPath()]
        );

        $currentPendingOffers      = array_slice(
            $organizationPendingOffers, $perPage * ($currentPendingOfferPage - 1),
            $perPage
        );
        $organizationPendingOffers = new Paginator(
            $currentPendingOffers, count($organizationPendingOffers), $perPage,
            $currentPendingOfferPage, ['pageName' => 'pending_offers', 'path' => Paginator::resolveCurrentPath()]
        );

        $currentPendingOrders      = array_slice(
            $organizationPendingOrders, $perPage * ($currentPendingOrderPage - 1),
            $perPage
        );
        $organizationPendingOrders = new Paginator(
            $currentPendingOrders, count($organizationPendingOrders), $perPage,
            $currentPendingOrderPage, ['pageName' => 'pending_orders', 'path' => Paginator::resolveCurrentPath()]
        );

        $currentRealizedOrders      = array_slice(
            $organizationRealizedOrders,
            $perPage * ($currentRealizedOrderPage - 1), $perPage
        );
        $organizationRealizedOrders = new Paginator(
            $currentRealizedOrders, count($organizationRealizedOrders),
            $perPage, $currentRealizedOrderPage,
            ['pageName' => 'realized_orders', 'path' => Paginator::resolveCurrentPath()]
        );
        $emails_received            = Email::where('from_email', $organisation->email)->where(
            'is_send',
            0
        )->orderBy('created_at', 'DESC')->paginate(10);
        $emails                     = Email::where('is_send', 1)->where(
            'to_email',
            $organisation->email
        )->orderBy('created_at', 'DESC')->paginate(10);

        return view(
            'institutions.show', compact(
                'organisation',
                'related_organizations',
                'organizationSurpluses',
                'organizationWanteds',
                'organizationPendingOffers',
                'organizationPendingOrders',
                'organizationRealizedOrders',
                'mailing_categories',
                'emails_received',
                'emails'
            )
        );
    }

    /**
     * CheckForExistence
     *
     * @param Request $request request.
     *
     * @return JsonResponse the response
     */
    public function checkForExistence(Request $request)
    {
        $name    = $request->input('name');
        $domain  = $request->input('domain');
        $city    = $request->input('city');
        $country = $request->input('country');

        $organisations = Organisation::where("city", "like", "%{$city}")
            ->where("country_id", "=", $country)
            ->where(
                static function ($query) use ($name, $domain) {
                                            $query->where('name', 'like', "%{$name}%")
                                                ->orWhere('synonyms', 'like', "%{$name}%")
                                                ->when(
                                                    $domain, static function ($query) use ($domain) {
                                                        $query->orWhere("domain_name", "like", "%{$domain}%");
                                                    }
                                                );;
                }
            )
                                     ->get();

        $contacts = Contact::where("city", "like", "%{$city}")
            ->where("country_id", "=", $country)
            ->where(
                static function ($query) use ($name, $domain) {
                                $query->where("first_name", "like", "%{$name}%")
                                    ->orWhere("last_name", "like", "%{$name}%")
                                    ->when(
                                        $domain, static function ($query) use ($domain) {
                                            $query->orWhere("domain_name", "like", "%{$domain}%");
                                        }
                                    );;
                }
            )
                           ->get();


        return response()->json(compact('organisations', 'contacts'));
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request             The request
     * @param object  $organisation        The org
     * @param object  $organisationToMerge The org to merge
     * @param string  $from                From
     * @param int     $source_id           Src
     *
     * @return Renderable
     */
    public function compare(Request $request, $organisation, $organisationToMerge, $from, $source_id): Renderable
    {
        // Flow to allow for contact-to-institution merge
        if ($request->has('extra-merge')) {
            if ($request->get('contact') == 1) {
                $toMerge = Contact::findOrFail($organisationToMerge);
                $merging = Organisation::findOrFail($organisation);
                $contact = 1;
            } else {
                $merging = Contact::findOrFail($organisationToMerge);
                $toMerge = Organisation::findOrFail($organisation);
                $contact = 0;
            }

            return view(
                'institutions.compare-organisation-contact', compact(
                    'toMerge',
                    'merging',
                    'from',
                    'source_id',
                    'contact'
                )
            );
        }

        // Regular flow
        $organisation = Organisation::findOrFail($organisation);

        $organisationToMerge = Organisation::findOrFail($organisationToMerge);

        return view('institutions.compare', compact('organisation', 'organisationToMerge', 'from', 'source_id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\OrganisationMergeRequest $request the request
     *
     * @return RedirectResponse
     * @throws \Exception
     */
    public function mergeContact(Request $request)
    {
        $contact   = $request->get('contact');
        $toMergeId = $request->input('toMergeId');
        $mergerId  = $request->input('mergerId');

        if ($contact === 0) {
            $toMerge = Contact::findOrFail($toMergeId);
            $merging = Organisation::findOrFail($mergerId);
        } else {
            $toMerge = Organisation::findOrFail($toMergeId);
            $merging = Contact::findOrFail($mergerId);
        }

        if ($request->input('check_name')) {
            $merging->name = $toMerge->name;
        }

        if ($request->input('check_type')) {
            $merging->organisation_type = $toMerge->organisation_type;
        }

        if ($request->input('check_email')) {
            $merging->email       = $toMerge->email;
            $domain_name          = substr($toMerge->email, strpos($toMerge->email, '@') + 1);
            $merging->domain_name = $domain_name;
        }

        if ($request->input('check_country')) {
            $merging->country_id = $toMerge->country_id;
        }

        if ($request->input('check_city')) {
            $merging->city = $toMerge->city;
        }

        if ($request->input('check_phone')) {
            if ($contact === 1) {
                $merging->phone = $toMerge->phone;
            } else {
                $merging->mobile_phone = $toMerge->phone;
            }
        }

        try {
            DB::beginTransaction();
            $merging->update();

            if ($contact === 1) {
                $toMerge->surpluses->each(
                    function (Surplus $surplus) use ($merging) {
                        $surplus->update(
                            [
                            'organisation_id' => $merging->id,
                            'contact_id'      => null,
                            ]
                        );
                    }
                );

                $toMerge->wanteds->each(
                    function (Wanted $wanted) use ($merging) {
                        $wanted->update(
                            [
                            'organisation_id' => $merging->id,
                            'contact_id'      => null,
                            ]
                        );
                    }
                );
            } else {
                $toMerge->surpluses->each(
                    function (Surplus $surplus) use ($merging) {
                        $surplus->update(
                            [
                            'organisation_id' => null,
                            'contact_id'      => $merging->id,
                            ]
                        );
                    }
                );

                $toMerge->wanteds->each(
                    function (Wanted $wanted) use ($merging) {
                        $wanted->update(
                            [
                            'organisation_id' => null,
                            'contact_id'      => $merging->id,
                            ]
                        );
                    }
                );
            }

            $toMerge->delete();
            DB::commit();
        } catch (Exception $e) {
            //\Log::error($e->getMessage());
            DB::rollBack();
        }

        if ($contact === 1) {
            return redirect(route('organisations.show', [$merging->id]));
        }

        return redirect(route('contacts.show', [$merging->id]));
    }

    /**
     * Merge organisation into another one.
     *
     * @param OrganisationMergeRequest $request THe request
     *
     * @return RedirectResponse
     * @throws Exception
     */
    public function merge(OrganisationMergeRequest $request)
    {
        $originalOrganization = Organisation::findOrFail($request->organization_id);
        $organizationToMerge  = Organisation::findOrFail($request->organizationToMerge_id);

        if ($request->input('check_name')) {
            $organizationToMerge->name = $originalOrganization->name;
        }

        if ($request->input('check_type')) {
            $organizationToMerge->organisation_type = $originalOrganization->organisation_type;
        }

        if ($request->input('check_email')) {
            $organizationToMerge->email       = $originalOrganization->email;
            $domain_name                      = substr(
                $originalOrganization->email,
                strpos($originalOrganization->email, '@') + 1
            );
            $organizationToMerge->domain_name = $domain_name;
        }

        if ($request->input('check_address')) {
            $organizationToMerge->address = $originalOrganization->address;
        }

        if ($request->input('check_zipcode')) {
            $organizationToMerge->zipcode = $originalOrganization->zipcode;
        }

        if ($request->input('check_country')) {
            $organizationToMerge->country_id = $originalOrganization->country_id;
        }

        if ($request->input('check_city')) {
            $organizationToMerge->city = $originalOrganization->city;
        }

        if ($request->input('check_phone')) {
            $organizationToMerge->phone = $originalOrganization->phone;
        }

        if ($request->input('check_fax')) {
            $organizationToMerge->fax = $originalOrganization->fax;
        }

        if ($request->input('check_website')) {
            $organizationToMerge->website = $originalOrganization->website;
        }

        if ($request->input('check_facebook_page')) {
            $organizationToMerge->facebook_page = $originalOrganization->facebook_page;
        }

        if ($request->input('check_vat_number')) {
            $organizationToMerge->vat_number = $originalOrganization->vat_number;
        }

        if ($request->input('check_level')) {
            $organizationToMerge->level = $originalOrganization->level;
        }

        if ($request->input('check_info_status')) {
            $organizationToMerge->info_status = $originalOrganization->info_status;
        }

        if ($request->input('check_remarks')) {
            $organizationToMerge->remarks = $originalOrganization->remarks;
        }

        if ($request->input('check_short_description')) {
            $organizationToMerge->short_description = $originalOrganization->short_description;
        }

        if ($request->input('check_public_zoos_relation')) {
            $organizationToMerge->public_zoos_relation = $originalOrganization->public_zoos_relation;
        }

        if ($request->input('check_animal_related_association')) {
            $organizationToMerge->animal_related_association = $originalOrganization->animal_related_association;
        }

        if (!$organizationToMerge->is_approved && $originalOrganization->is_approved == true) {
            $organizationToMerge->is_approved = true;
        }

        $organizationToMerge->synonyms = "{$originalOrganization->name}\r\n{$organizationToMerge->synonyms}";

        $organizationToMerge->update();
        $organizationToMerge->contacts()->saveMany($originalOrganization->contacts);
        $organizationToMerge->surpluses()->saveMany($originalOrganization->surpluses);
        $organizationToMerge->wanteds()->saveMany($originalOrganization->wanteds);
        $organizationToMerge->interest()->sync($originalOrganization->interest);
        $organizationToMerge->associations()->sync($originalOrganization->associations);
        $organizationToMerge->emails()->saveMany($originalOrganization->emails);

        $originalOrganization->delete();

        return redirect(route('organisations.show', [$organizationToMerge->id]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Organisation $organisation The org
     * @param Request                  $request      The request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Organisation $organisation, Request $request)
    {
        $organization_types  = OrganisationType::orderBy('key')->pluck('label', 'key');
        $countries           = Country::orderBy('name')->pluck('name', 'id');
        $organization_levels = OrganisationLevel::get();
        $infoStatuses        = OrganisationInfoStatus::get();
        $interest_sections   = InterestSection::orderBy('key', 'desc')->get();
        $associations        = Association::orderBy('key')->get();
        $mailing_categories  = ContactMailingCategory::get();
        $specialties         = Specialty::get();
        $edit                = $request->edit ?? "";
        $edit_id             = $request->edit_id ?? "";

        $organizationInterestSections = $organisation->interest()->pluck('key');
        $organizationAssociations     = $organisation->associations()->pluck('key');

        return view(
            'institutions.edit', compact(
                'organisation',
                'organization_types',
                'countries',
                'organization_levels',
                'infoStatuses',
                'interest_sections',
                'organizationInterestSections',
                'associations',
                'organizationAssociations',
                'mailing_categories',
                'specialties',
                'edit',
                'edit_id'
            )
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\OrganisationUpdateRequest $request      The request
     * @param \App\Models\Organisation                     $organisation The org
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(OrganisationUpdateRequest $request, Organisation $organisation)
    {
        $domain_name = ($request->email != null) ? substr($request->email, strpos($request->email, '@') + 1) : null;

        if ($request->domain_name == null) {
            $request['domain_name'] = $domain_name;
        }

        if ($request->filled('synonyms')) {
            $request->merge(['synonyms' => strtolower($request->input('synonyms'))]);
        }

        $organisation->update($request->all());

        $organisation->interest()->sync($request->interest_section);
        $organisation->associations()->sync($request->associations);

        if (!empty($request->edit) && !empty($request->edit_id) && $request->edit == "offer") {
            return redirect(route('offers.show', $request->edit_id));
        } else {
            return redirect(route('organisations.show', [$organisation->id]));
        }
    }

    /**
     * Edit selected items.
     *
     * @param \Illuminate\Http\Request $request The request
     *
     * @return \Illuminate\Http\Response
     */
    public function editSelectedRecords(Request $request)
    {
        if (count($request->items) > 0) {
            foreach ($request->items as $id) {
                $organisation = Organisation::findOrFail($id);

                if (isset($request->institution_type)) {
                    $organisation->update(['organisation_type' => $request->institution_type]);
                }

                if (isset($request->level)) {
                    if ($request->level === 'empty') {
                        $organisation->update(['level' => null]);
                    } else {
                        $organisation->update(['level' => $request->level]);
                    }
                }

                if (isset($request->country_id)) {
                    $organisation->update(['country_id' => $request->country_id]);
                }

                if (isset($request->city)) {
                    $organisation->update(['city' => $request->city]);
                }

                if (isset($request->canonical_name)) {
                    $organisation->update(['canonical_name' => $request->canonical_name]);
                }

                if (isset($request->mailing_category)) {
                    $organisation->update(['mailing_category' => $request->mailing_category]);
                }

                if ($request->make_institution_name == 1 && $organisation->organisation_type != null && $organisation->city != null) {
                    $institution_name = strtoupper($organisation->city) . ' ' . $organisation->type->key;
                    $organisation->update(['name' => $institution_name]);
                }

                if ($request->make_website == 1 && $organisation->email != null) {
                    $domain_name = substr($organisation->email, strpos($organisation->email, '@') + 1);
                    $organisation->update(['website' => 'wwww.' . $domain_name]);
                }

                if ($request->associations > 0) {
                    $organisation->associations()->sync($request->associations);
                }
            }
        }

        return response()->json(['message' => 'The selection records has been updated successfully']);
    }

    /**
     * Edit Level.
     *
     * @param \Illuminate\Http\Request $request The request
     *
     * @return \Illuminate\Http\Response
     */
    public function editLevel(Request $request)
    {
        $organisation = Organisation::find($request->id);
        if (isset($request->level)) {
            $organisation->update(['level' => $request->level]);
        }

        return response()->json(['message' => 'The level field has been updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request      $request      The req
     * @param Organisation $organisation The org
     *
     * @return RedirectResponse|JsonResponse
     * @throws \Exception
     */
    public function destroy(Request $request, Organisation $organisation)
    {
        $handOver     = $request->input('handover_id');
        $handOverType = $request->input('handover_type');
        $toDelete     = $request->input('to_delete_id');

        $organisation->load(
            [
            'wanteds',
            'surpluses',
            'contacts.offers',
            'contacts.orders_contact_client',
            'contacts.orders_contact_supplier',
            'contacts.orders_contact_origin',
            'contacts.orders_contact_destination',
            ]
        );

        if ($handOver && $handOverType) {
            $newParent = $handOverType === "institute" ? Organisation::find($handOver) : Contact::find($handOver);

            try {
                DB::beginTransaction();
                $organisation->contacts->each(
                    static function (Contact $contact) use ($newParent) {
                        $contact->update(['organisation_id' => $newParent->id]);
                    }
                );

                $organisation->surpluses->each(
                    static function (Surplus $surplus) use ($newParent) {
                        $surplus->update(['organisation_id' => $newParent->id]);
                    }
                );

                $organisation->wanteds->each(
                    static function (Wanted $wanted) use ($newParent) {
                        $wanted->update(['organisation_id' => $newParent->id]);
                    }
                );

                $organisation->delete();

                DB::commit();

                return response()->json(
                    [
                    'success' => true,
                    ]
                );
            } catch (\Exception $e) {
                DB::rollBack();

                return response()->json(
                    [
                    'success' => false,
                    'message' => $e->getMessage(),
                    ]
                );
            }
        }


        $validator = Validator::make($organisation->toArray(), []);

        if (!$validator->errors()->has('surpluses') && $organisation->surpluses->count() > 0) {
            $validator->errors()->add('surpluses', 'The institution is related with surplus records.');
        }

        if (!$validator->errors()->has('wanteds') && $organisation->wanteds->count() > 0) {
            $validator->errors()->add('wanteds', 'The institution is related with wanted records.');
        }

        if ($organisation->contacts->count() > 0) {
            foreach ($organisation->contacts as $contact) {
                if (!$validator->errors()->has('offers') && $contact->offers->count() > 0) {
                    $validator->errors()->add('offers', 'The institution has contacts related with offers.');
                }

                if (!$validator->errors()->has('orders') && ($contact->orders_contact_client->count() > 0 || $contact->orders_contact_supplier->count() > 0 || $contact->orders_contact_origin->count() > 0 || $contact->orders_contact_destination->count() > 0)) {
                    $validator->errors()->add('orders', 'The institution has contacts related with orders.');
                }

                if (!$validator->errors()->has('invoices') && $contact->invoices->count() > 0) {
                    $validator->errors()->add('invoices', 'The institution has contacts related with invoices.');
                }
            }
        }

        if ($request->expectsJson()) {
            if ($validator->errors()->count() > 0) {
                return response()->json(
                    [
                    'success' => false,
                    'data'    => $validator->getMessageBag(),
                    ]
                );
            } else {
                $organisation->delete();
                return response()->json(['success' => true]);
            }
        }

        if ($validator->errors()->count() > 0) {
            return redirect(route('organisations.show', $organisation))->withErrors($validator);
        } else {
            $organisation->delete();
            return redirect(route('organisations.index'));
        }
    }

    /**
     * Remove the selected items.
     *
     * @param \Illuminate\Http\Request $request The req
     *
     * @return \Illuminate\Http\Response
     */
    public function delete_items(Request $request) //phpcs:ignore
    {
        if (count($request->items) > 0) {
            $institutionsNotDeleted = [];

            foreach ($request->items as $id) {
                $contactError = false;

                $institutionToDelete = Organisation::findOrFail($id);

                if ($institutionToDelete->surpluses->count() > 0) {
                    $contactError = true;
                } elseif ($institutionToDelete->wanteds->count() > 0) {
                    $contactError = true;
                }

                if ($institutionToDelete->contacts->count() > 0) {
                    foreach ($institutionToDelete->contacts as $contact) {
                        if ($contact->offers->count() > 0) {
                            $contactError = true;
                        } elseif ($contact->orders_contact_client->count() > 0 || $contact->orders_contact_supplier->count() > 0 || $contact->orders_contact_origin->count() > 0 || $contact->orders_contact_destination->count() > 0) {
                            $contactError = true;
                        } elseif ($contact->invoices->count() > 0) {
                            $contactError = true;
                        }

                        if ($contactError) {
                            break;
                        }
                    }
                }

                if ($contactError) {
                    array_push($institutionsNotDeleted, $institutionToDelete->id);
                } else {
                    $institutionToDelete->delete();
                }
            }
        }

        return response()->json(['message' => (count($institutionsNotDeleted) > 0) ? 'Some institutions were not deleted because are related with surplus and wanted, or have contacts related with: offers, or orders, or invoices.' : null]);
    }

    /**
     * Filter organizations.
     *
     * @param \Illuminate\Http\Request $request The req
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function filterOrganizations(Request $request)
    {
        // Set session organization filter
       $data = [];
        foreach ($request->query() as $key => $row) {
            if ((!empty($row) || $row == "0") && substr($key, 0, 6) !== 'hidden') {
                $data[$key] = $row;
            }
        }
        session(['organization.filter' => $data]);

        return redirect(route('organisations.index'));
    }

    /**
     * Records per page.
     *
     * @param \Illuminate\Http\Request $request The req
     *
     * @return \Illuminate\Http\RedirectResponse|\CompiledRouteCollection
     */
    public function recordsPerPage(Request $request)
    {
        $query                   = session('organization.filter');
        $query['recordsPerPage'] = $request->recordsPerPage;
        session(['organization.filter' => $query]);

        return redirect(route('organisations.index'));
    }

    /**
     * Remove from organization session.
     *
     * @param string $key The key
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function removeFromOrganizationSession($key)
    {
        $query = session('organization.filter');
        Arr::forget($query, $key);
        session(['organization.filter' => $query]);

        return redirect(route('organisations.index'));
    }

    /**
     * Get doubles view.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function doublesView()
    {
        $organization_types = OrganisationType::orderBy('key')->pluck('label', 'key');
        $countries          = Country::orderBy('name')->pluck('name', 'id');
        $criteria           = null;

        return view('institutions.find_doubles', compact('organization_types', 'countries', 'criteria'));
    }

    /**
     * Filter organizations doubles.
     *
     * @param \Illuminate\Http\Request $request The request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function filterOrganizationsDoubles(Request $request)
    {
        // Set session organization filter
        session(['organization_doubles.filter' => $request->query()]);

        return redirect(route('organisations.searchDoubles'));
    }

    /**
     * Filter doubles.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function searchDoubles()
    {
        $organisations = Organisation::select('*');

        // Check if filter is set on session
        if (session()->has('organization_doubles.filter')) {
            $request = session('organization_doubles.filter');

            if (isset($request['filter_organisation_type'])) {
                $organisations->where('organisation_type', $request['filter_organisation_type']);
            }

            if (isset($request['filter_country'])) {
                $organisations->where('country_id', $request['filter_country']);
            }

            $criteria = $request['filter_doubles_by'];
            if (isset($criteria)) {
                switch ($criteria) {
                case 'name':
                    $organisations->whereNotNull('name')
                        ->where(DB::raw('TRIM(name)'), '<>', '');
                    break;
                case 'email':
                    $organisations->whereNotNull('email')
                        ->where(DB::raw('TRIM(email)'), '<>', '');
                    break;
                case 'domain_name':
                    $organisations->whereNotNull('domain_name')
                        ->where(DB::raw('TRIM(domain_name)'), '<>', '');
                    break;
                }
            }
        }

        $organisations = $organisations->get();

        if (isset($criteria)) {
            $organisations = $organisations->sortBy($criteria);
            $organisations = $organisations->groupBy($criteria);
        }

        $organization_types = OrganisationType::orderBy('key')->pluck('label', 'key');
        $countries          = Country::orderBy('name')->pluck('name', 'id');

        $filter_organizations = [];
        foreach ($organisations as $organization_group) {
            if (count($organization_group) > 1) {
                foreach ($organization_group as $organizationDouble) {
                    array_push($filter_organizations, $organizationDouble);
                }
            }
        }

        $per_page = 100;

        /*$total = count($filter_organizations);

        $current_page = $request->input("page") ?? 1;

        $starting_point = ($current_page * $per_page) - $per_page;

        $organisations = array_slice($filter_organizations, $starting_point, $per_page, true);

        $organisations = new Paginator($organisations, $total, $per_page, $current_page, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);*/

        $currentPage = Paginator::resolveCurrentPage();

        $organisations = array_slice($filter_organizations, $per_page * ($currentPage - 1), $per_page);
        $organisations = new Paginator(
            $organisations, count($filter_organizations), $per_page, $currentPage,
            ['path' => Paginator::resolveCurrentPath()]
        );

        return view(
            'institutions.find_doubles',
            compact('organisations', 'organization_types', 'countries', 'criteria')
        );
    }

    /**
     * Show the form for creating a new contact.
     *
     * @param int $organization_id The org ID
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function createContact(int $organization_id)
    {
        $organization       = Organisation::findOrFail($organization_id);
        $mailing_categories = ContactMailingCategory::get();
        $interest_sections  = InterestSection::orderBy('key', 'desc')->get();
        $countries          = Country::orderBy('name')->pluck('name', 'id');

        return view(
            'institutions.create_contact',
            compact('mailing_categories', 'organization', 'interest_sections', 'countries')
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ContactCreateRequest $request The request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function storeContact(ContactCreateRequest $request)
    {
        $organization = Organisation::findOrFail($request->organization_id);

        $domain_name = ($request->email != null) ? substr($request->email, strpos($request->email, '@') + 1) : null;

        $contact                   = new Contact();
        $contact->email            = $request->contact_email;
        $contact->domain_name      = $domain_name;
        $contact->source           = 'crm';
        $contact->title            = $request->title;
        $contact->first_name       = $request->first_name;
        $contact->last_name        = $request->last_name;
        $contact->country_id       = $request->country_id;
        $contact->city             = $request->city;
        $contact->mobile_phone     = $request->mobile_phone;
        $contact->organisation_id  = $organization->id;
        $contact->position         = $request->position;
        $contact->mailing_category = $request->mailing_category;
        $contact->inserted_by      = Auth::id();
        $contact->save();

        $contact->interest_sections()->sync($request->interest_section);

        return redirect(route('organisations.show', $organization));
    }

    /**
     * Show the form for editing the specified contact.
     *
     * @param \App\Models\Contact $contact         THe contact.
     * @param int                 $organization_id The org id.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showContact(Contact $contact, int $organization_id)
    {
        $organization = Organisation::findOrFail($organization_id);

        $mailing_categories     = ContactMailingCategory::get();
        $member_approved_status = ContactApprovedStatus::get();

        return view(
            'institutions.edit_contact',
            compact('organization', 'contact', 'mailing_categories', 'member_approved_status')
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\ContactUpdateRequest $request The request
     * @param \App\Models\Contact                     $contact The contact
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function updateContact(ContactUpdateRequest $request, Contact $contact)
    {
        $organization = Organisation::findOrFail($request->organization_id);

        if ($request->mailing_category != null) {
            $request['mailing_category'] = $request->mailing_category;
        }
        if ($contact->source == 'website' && $request->member_approved_status != null) {
            $request['member_approved_status'] = $request->member_approved_status;
        }
        $contact->update($request->all());

        return redirect(route('organisations.show', $organization));
    }

    /**
     * Remove the specified contact from storage.
     *
     * @param \App\Models\Contact $contact         THe contact
     * @param int                 $organization_id The org ID
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroyContact(Contact $contact, int $organization_id)
    {
        $organization = Organisation::findOrFail($organization_id);

        $validator = Validator::make($contact->toArray(), []);

        if ($contact->surpluses->count() > 0) {
            $validator->errors()->add('surpluses', 'This contact has surplus records.');
        }
        if ($contact->wanteds->count() > 0) {
            $validator->errors()->add('wanteds', 'This contact has wanted records.');
        }
        if ($contact->offers->count() > 0) {
            $validator->errors()->add('offers', 'This contact is related with offers.');
        }
        if ($contact->orders_contact_client->count() > 0 || $contact->orders_contact_supplier->count() > 0 || $contact->orders_contact_origin->count() > 0 || $contact->orders_contact_destination->count() > 0) {
            $validator->errors()->add('orders', 'This contact is related with orders.');
        }
        if ($contact->invoices->count() > 0) {
            $validator->errors()->add('invoices', 'This contact is related with invoices.');
        }

        if ($validator->errors()->count() > 0) {
            $errors = $validator->errors();

            $mailing_categories     = ContactMailingCategory::get();
            $member_approved_status = ContactApprovedStatus::get();

            return view(
                'institutions.edit_contact',
                compact('organization', 'contact', 'mailing_categories', 'member_approved_status', 'errors')
            );
        } else {
            $contact->delete();

            return redirect(route('organisations.show', $organization));
        }
    }

    /**
     * Export
     *
     * @param Request $request The req
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(Request $request)
    {
        if (session()->has('organization.filter')) {
            $filter = session('organization.filter');
        } else {
            $filter = [];
        }
        $organisations = DB::table('organisations')
           ->selectRaw('id')
           ->selectRaw('name')
           ->selectRaw("'I' as type_key")
           ->selectRaw('address')
           ->selectRaw('(SELECT name FROM countries WHERE id = country_id) as country')
           ->selectRaw('city')
           ->selectRaw('email')
           ->selectRaw('level')
           ->selectRaw('phone')
           ->selectRaw('website')
           ->selectRaw('facebook_page')
           ->selectRaw('domain_name');

        $contacts = DB::table('contacts')
           ->selectRaw('contacts.id as id')
           ->selectRaw('CONCAT(IFNULL(first_name, ""), " ", IFNULL(last_name, "")) as name')
           ->selectRaw("'C' as type_key")
           ->selectRaw("' ' as address")
           ->selectRaw('(SELECT name FROM countries WHERE id = country_id) as country')
           ->selectRaw('city as city')
           ->selectRaw('contacts.email as email')
           ->selectRaw('null as level')
           ->selectRaw('mobile_phone as phone')
           ->selectRaw('null as website')
           ->selectRaw("' ' as facebook_page")
           ->selectRaw('domain_name')
           ->where('deleted_at', '=', null);

        $institutions = $organisations->whereIn('id', explode(',', $request->oitems));
        $contacts = $contacts->whereIn('id', explode(',', $request->citems));
        $institutions = $institutions->union($contacts);

        if (!empty($filter['orderByField']) && !empty($filter['orderByDirection'])) {
            $institutions = $institutions->orderBy(
                $filter['orderByField'],
                $filter['orderByDirection']
            );
        }
        $institutions = $institutions->get();

        $file_name = 'Institutions list ' . Carbon::now()->format('Y-m-d') . '.xlsx';

        $export = new InstitutionsExport($institutions);

        return Excel::download($export, $file_name);
    }

    /**
     * Filter organizations doubles.
     *
     * @param \Illuminate\Http\Request $request The request
     *
     * @return \Illuminate\Http\Response|null
     */
    public function validateCanonical(Request $request)
    {
        if (!empty($request->email)) {
            $email     = $request->email;
            $canonical = $request->search;
            $email     = explode('@', $email);
            if ($email !== null) {
                $search = DomainNameLink::where('domain_name', $email[1])->first();
                if (!empty($search) && empty($canonical)) {
                    return response()->json(
                        [
                        'error'   => true,
                        'message' => 'The canonical name for that domain is <strong>' . $search['canonical_name'] . '</strong>',
                        ]
                    );
                }
                if (!empty($search) && !empty($canonical) && ($search['canonical_name'] != $canonical)) {
                    return response()->json(
                        [
                        'error'   => true,
                        'message' => 'Please make sure the canonical name is correct. There is a fixed canonical name for this domain, and it is <strong>' . $search['canonical_name'] . '</strong>',
                        ]
                    );
                }
            }
            return null;
        }
        return null;
    }

    /**
     * Shownewanimals
     *
     * @param Request $request The request
     *
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function showNewAnimals(Request $request)
    {
        $data = $this->getDataNewAnimals();

        $data['email_body'] = view('emails.list-animal-new-contact', $data)->render();

        return view('institutions.email_new_animal_to_client', $data)->render();
    }

    /**
     * InstitutionSendNewAnimal
     *
     * @param Request $request The request
     *
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function institutionSendNewAnimal(Request $request)
    {
        $this->validate(
            $request, [
            'email_from'    => 'required',
            'email_subject' => 'required',
            'email_body'    => 'required',
            ]
        );
        $surplus = Surplus::where('new_animal', 1)->get();

        if (!empty($surplus->toArray())) {
            $contacts = Contact::GetContacts()->whereHas(
                'organisation', function ($query) use ($request) {
                    $query->where('level', 'A');
                }
            )
                               ->where('mailing_category', 'all_mailings')
                               ->select('*', 'contacts.email as email')
                               ->whereNotNull('contacts.email')
                               ->get()
                               ->pluck('letter_name', 'email');

            $organisations = Organisation::where('level', 'A')
                ->where('mailing_category', 'all_mailings')
                ->whereNotNull('email')
                ->get();

            $organisations_level = [];

            foreach ($organisations as $key => $row) {
                $letter_name = 'Mr./Mrs.';
                if (!empty($row->contacts)) {
                    foreach ($row->contacts as $key_contact => $value) {
                        if ($value->email == $row->email) {
                            $letter_name = $value['letter_name'];
                        }
                    }
                }
                $organisations_level[$row['email']] = $letter_name;
            }

            $result = array_merge($contacts->toArray(), $organisations_level);

            foreach ($result as $key => $row) {
                OrganisationSendAnimalNew::create(
                    [
                    'name'       => ($row !== ' ') ? $row : 'Mr./Mrs.', 'email' => $key,
                    'email_from' => $request->email_from, 'email_subject' => $request->email_subject,
                    'email_body' => $request->email_body,
                    ]
                );
            }
            OrganisationSendAnimalNew::create(
                [
                'name'       => 'John123', 'email' => 'johnrens@zoo-services.com',
                'email_from' => $request->email_from, 'email_subject' => $request->email_subject,
                'email_body' => $request->email_body,
                ]
            );

            foreach ($surplus->toArray() as $row) {
                $animal               = Surplus::find($row['id']);
                $animal['new_animal'] = 0;
                $animal->save();
            }

            $data         = $this->getDataNewAnimals($request->item);
            $data['date'] = '';

            $data['table_new_animals']    = view('emails.list-animal-new-contact', $data)->render();
            $data['table_delete_animals'] = view('institutions.table-new-animals', $data)->render();

            return response()->json(
                [
                'error' => false, 'message' => 'The e-mail is scheduled for sending', 'data' => $data,
                ]
            );
        } else {
            return response()->json(['error' => true, 'message' => 'There are no new animals to send']);
        }
    }

    /**
     * DeleteItemsNewAnimals
     *
     * @param Request $request The request
     *
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function deleteItemsNewAnimals(Request $request)
    {
        if (!empty($request->item)) {
            foreach ($request->item as $row) {
                $surplu = Surplus::find($row);
                if (empty($surplu['new_animal']) || $surplu['new_animal'] = 0) {
                    $surplu['new_animal'] = 1;
                    $surplu->save();
                }
            }
            $data              = $this->getDataNewAnimals($request->item, $request->filter_updated_at_from);
            $table_new_animals = view('emails.list-animal-new-contact', $data)->render();

            return response()->json(
                [
                'error'             => false, 'message' => 'Surpluses were successfully removed from the list',
                'table_new_animals' => $table_new_animals,
                ]
            );
        } else {
            return response()->json(['error' => true, 'message' => 'You must select items to delete.']);
        }
    }

    /**
     * GetDataNewAnimals
     *
     * @param array $ids         The ids
     * @param mixed $date_filter The date filter
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getDataNewAnimals($ids = [], $date_filter = null)
    {
        $date = Carbon::now();
        $date = $date->subDay(120);
        if (!empty($date_filter)) {
            $date = $date_filter;
        } else {
            $date = $date->format('Y-m-d');
        }
        if (!empty($ids)) {
            $data['surplus'] = Surplus::join('animals', 'animals.id', '=', 'surplus.animal_id')
                ->select('*', 'surplus.id as id')
                ->where('surplus.new_animal', 1)
                ->whereIn('surplus.id', $ids)
                ->where('origin', '!=', 'stuffed')
                ->where('surplus_status', '!=', 'collection')
                ->whereDate('surplus.created_at', '>=', $date)
                ->orderBy('animals.code_number', 'ASC')
                ->get();
        } else {
            $data['surplus'] = Surplus::join('animals', 'animals.id', '=', 'surplus.animal_id')
                ->select('*', 'surplus.id as id')
                ->where('surplus.new_animal', 1)
                ->where('origin', '!=', 'stuffed')
                ->where('surplus_status', '!=', 'collection')
                ->whereDate('surplus.created_at', '>=', $date)
                ->orderBy('animals.code_number', 'ASC')
                ->get();
        }

        if (!empty($date_filter)) {
            $data['surplus_list'] = Surplus::join('animals', 'animals.id', '=', 'surplus.animal_id')
                ->select('*', 'surplus.id as id', 'surplus.created_at as created_at')
                ->where('origin', '!=', 'stuffed')
                ->where('surplus_status', '!=', 'collection')
                ->whereDate('surplus.created_at', '>=', $date)
                ->orderBy('surplus.created_at', 'DESC')
                ->get();
        } else {
            $data['surplus_list'] = Surplus::join('animals', 'animals.id', '=', 'surplus.animal_id')
                ->select('*', 'surplus.id as id', 'surplus.created_at as created_at')
                ->where('origin', '!=', 'stuffed')
                ->where('surplus_status', '!=', 'collection')
                ->where('surplus.new_animal', 1)
                ->whereDate('surplus.created_at', '>=', $date)
                ->orderBy('surplus.created_at', 'DESC')
                ->get();
        }

        $data['date'] = '';

        $data['email_from']      = 'info@zoo-services.com';
        $data['email_subject']   = 'Recent surplus species';
        $data['email_title']     = 'Dear [name_client]';
        $data['email_initially'] = 'We recently got following species offered by zoological institutions:';
        $data['email_footer']    = 'In case you have interest, please do not hesitate to contact us for more information.';

        return $data;
    }

    /**
     * ExportEmailInstitutionsLevelA
     *
     * @param Request $request The request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function exportEmailInstitutionsLevelA(Request $request)
    {
        $contacts = Contact::GetContacts()->whereHas(
            'organisation', function ($query) use ($request) {
                $query->where('level', 'A');
            }
        )
                           ->where('mailing_category', 'all_mailings')
                           ->select('contacts.email as email', 'contacts.first_name as name')
                           ->whereNotNull('contacts.email')
                           ->get()
                           ->pluck('name', 'email');

        $organisations = Organisation::where('level', 'A')
            ->where('mailing_category', 'all_mailings')
            ->select('email', 'name')
            ->whereNotNull('email')
            ->get()
            ->pluck('name', 'email');
        $result        = array_merge($contacts->toArray(), $organisations->toArray());

        $institutions = [];

        foreach ($result as $key => $row) {
            array_push($institutions, ['name' => ($row !== ' ') ? $row : 'customer', 'email' => $key]);
        }
        array_push($institutions, ['name' => 'John', 'email' => 'johnrens@zoo-services.com']);

        $file_name = 'Institutions list level A' . Carbon::now()->format('Y-m-d') . '.xlsx';

        $export = new InstitutionsExportLevelA($institutions);

        return Excel::download($export, $file_name);
    }

    /**
     * ResetListEmailNewOrganisation
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function resetListEmailNewOrganisation()
    {
        $organisations = Organisation::where('new_organisation', 1)->get();
        if (!empty($organisations)) {
            foreach ($organisations as $row) {
                $row['new_organisation'] = 0;
                $row->save();
            }
        }
        $title_dash = 'Organisations';

        return view('components.reset_list_email_new', compact('title_dash'));
    }

    /**
     * FilterDateNewAnimals
     *
     * @param Request $request The request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|null
     */
    public function filterDateNewAnimals(Request $request)
    {
        if (!empty($request->filter_updated_at_from)) {
            $data              = $this->getDataNewAnimals([], $request->filter_updated_at_from);
            $table_new_animals = view('institutions.table-new-animals', $data)->render();

            return response()->json(
                [
                'error'             => false, 'message' => 'Surpluses were successfully removed from the list',
                'table_new_animals' => $table_new_animals,
                ]
            );
        }
        return null;
    }

    /**
     * Get contacts for organisation
     *
     * @param Organisation $organisation The org
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getContactsForOrganisation(Organisation $organisation)
    {
        $contacts = $organisation->contacts()->get();

        return response()->json(
            [
            'organisation' => $organisation->toArray(),
            'data'         => $contacts,
            ]
        );
    }
}
