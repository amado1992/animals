<!-- Sidebar -->
<div class="siderbar-content hide_sidebar" data-show="false">
    <ul class="navbar-nav sidebar toggled" id="accordionSidebar">

        <!-- Sidebar - Brand -->
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('home') }}">
            <div class="sidebar-brand-icon">
                <img src="/img/logo-zooservices.png" style="width: 40px" />
            </div>
            <div class="sidebar-brand-text mx-3">Zoo Services</div>
        </a>

        <li class="nav-item my-0 {{ Nav::isRoute('tasks.index') }}">
            <a class="nav-link" href="{{ route('tasks.index') }}">
                <i class="fas fa-fw fa-tasks"></i>
                <span>{{ __('Tasks') }}</span>
            </a>
        </li>

        <li class="nav-item my-0">
            <a class="nav-link {{ in_array(Route::currentRouteName(), ['inbox.index']) ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseInbox" aria-expanded="true" aria-controls="collapseProjects">
                <i class="fas fa-fw fa-envelope"></i>
                <span>{{ __('Emails') }}</span>
            </a>
            <div id="collapseInbox" class="collapse" aria-labelledby="headingContacts" data-parent="#accordionSidebar">
                <div class="bg-white py-1 collapse-inner rounded">
                    <h6 class="collapse-header">Emails</h6>
                    <a class="collapse-item" href="{{ route('inbox.index') }}">{{ __('Inbox') }}</a>
                    <a class="collapse-item" href="{{ route('labels.index') }}">{{ __('Labels') }}</a>
                    <a class="collapse-item" href="{{ route('colors.index') }}">{{ __('Colors') }}</a>
                </div>
            </div>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider my-0">

        @if (Auth::user()->hasPermission('institutions.read'))
            <li class="nav-item my-0 {{ Nav::isRoute('organisations.index') }}">
                <a class="nav-link" href="{{ route('organisations.index') }}">
                    <i class="fas fa-fw fa-building"></i>
                    <span>{{ __('Institutions') }}</span>
                </a>
            </li>
        @endif

        @if (Auth::user()->hasPermission('contacts.read'))
            <li class="nav-item my-0">
                <a class="nav-link {{ in_array(Route::currentRouteName(), ['contacts.index', 'contacts-approve', 'contacts-deleted']) ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseContacts" aria-expanded="true" aria-controls="collapseProjects">
                    <i class="fas fa-fw fa-address-card"></i>
                    <span>{{ __('Contacts') }}</span>
                </a>
                <div id="collapseContacts" class="collapse" aria-labelledby="headingContacts" data-parent="#accordionSidebar">
                    <div class="bg-white py-1 collapse-inner rounded">
                        <h6 class="collapse-header">Contacts</h6>
                        <a class="collapse-item" href="{{ route('contacts.index') }}">{{ __('Contacts') }}</a>
                        @if (Auth::user()->hasPermission('contacts.approve-members'))
                            <a class="collapse-item" href="{{ route('contacts-approve.index') }}">{{ __('Contacts to approve') }}</a>
                        @endif
                    </div>
                </div>
            </li>
        @endif

        <hr class="sidebar-divider my-0">

        @if (Auth::user()->hasPermission(['standard-surplus.read', 'crates.read', 'airfreights.read', 'standard-wanted.read']))
            <li class="nav-item my-0">
                <a class="nav-link {{ in_array(Route::currentRouteName(), ['our-surplus.index', 'crates.index', 'airfreights.index', 'our-wanted.index']) ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseProducts" aria-expanded="true" aria-controls="collapseProducts">
                    <i class="fas fa-fw fa-hippo"></i>
                    <span>{{ __('Products') }}</span>
                </a>
                <div id="collapseProducts" class="collapse" aria-labelledby="headingProducts" data-parent="#accordionSidebar">
                    <div class="bg-white py-1 collapse-inner rounded">
                        <h6 class="collapse-header">Products</h6>
                        @if (Auth::user()->hasPermission('standard-surplus.read'))
                            <a class="collapse-item" href="{{ route('our-surplus.index') }}">{{ __('Stock-standardprices') }}</a>
                        @endif
                        @if (Auth::user()->hasPermission('crates.read'))
                            <a class="collapse-item" href="{{ route('crates.index') }}">{{ __('Crates') }}</a>
                        @endif
                        @if (Auth::user()->hasPermission('airfreights.read'))
                            <a class="collapse-item" href="{{ route('airfreights.index') }}">{{ __('Airfreight') }}</a>
                        @endif
                        @if (Auth::user()->hasPermission('standard-wanted.read'))
                            <a class="collapse-item" href="{{ route('our-wanted.index') }}">{{ __('Our Wanted') }}</a>
                        @endif
                   </div>
               </div>
           </li>
    @endif

    @if (Auth::user()->hasPermission('surplus-suppliers.read'))
        <li class="nav-item my-0 {{ Nav::isRoute('surplus.index') }}">
            <a class="nav-link" href="{{ route('surplus.index') }}">
                <i class="fas fa-fw fa-store"></i>
                <span>{{ __('Surplus of suppliers') }}</span>
            </a>
        </li>
        <li class="nav-item my-0 {{ Nav::isRoute('surplus-collection.index') }}">
            <a class="nav-link" href="{{ route('surplus-collection.index') }}">
                <i class="fas fa-fw fa-paw"></i>
                <span>{{ __('Collections') }}</span>
            </a>
        </li>
    @endif

    @if (Auth::user()->hasPermission('wanted-clients.read'))
        <li class="nav-item my-0 {{ Nav::isRoute('wanted.index') }}">
            <a class="nav-link" href="{{ route('wanted.index') }}">
                <i class="fas fa-fw fa-hand-paper"></i>
                <span>{{ __('Wanted of clients') }}</span>
            </a>
        </li>
    @endif

    @if (Auth::user()->hasPermission('offers.read'))
        <li class="nav-item my-0 {{ Nav::isRoute('offers.index') }}">
            <a class="nav-link" href="{{ route('offers.index') }}">
                <i class="fas fa-fw fa-signature"></i>
                <span>{{ __('Offers') }}</span>
            </a>
        </li>
    @endif

    @if (Auth::user()->hasPermission('orders.read'))
        <li class="nav-item my-0 {{ Nav::isRoute('orders.index') }}">
            <a class="nav-link" href="{{ route('orders.index') }}">
                <i class="fas fa-fw fa-suitcase"></i>
                <span>{{ __('Orders') }}</span>
            </a>
        </li>
    @endif

    @if (Auth::user()->hasPermission('invoices.read'))
        <li class="nav-item my-0 {{ Nav::isRoute('invoices.index') }}">
            <a class="nav-link" href="{{ route('invoices.index') }}">
                <i class="fas fa-fw fa-file-invoice-dollar"></i>
                <span>{{ __('Invoices') }}</span>
            </a>
        </li>
    @endif

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

        <!-- Divider -->
        <hr class="sidebar-divider my-0">

        @if (Auth::user()->hasPermission(['codes.read', 'zoo-associations.read', 'standard-texts.read', 'interesting-websites.read', 'guidelines.read', 'general-documents.read']))
            <li class="nav-item my-0">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseIntern" aria-expanded="true" aria-controls="collapseIntern">
                    <i class="fas fa-fw fa-info"></i>
                    <span>{{ __('Intern') }}</span>
                </a>
                <div id="collapseIntern" class="collapse" aria-labelledby="headingIntern" data-parent="#accordionSidebar">
                    <div class="bg-white py-1 collapse-inner rounded">
                        <h6 class="collapse-header">Intern</h6>
                        @if (Auth::user()->hasPermission('codes.read'))
                            <a class="collapse-item" href="{{ route('codes.index') }}">{{ __('Codes') }}</a>
                        @endif
                        @if (Auth::user()->hasPermission('zoo-associations.read'))
                            <a class="collapse-item" href="{{ route('zoo-associations.index') }}">{{ __('Zoo associations') }}</a>
                        @endif
                        @if (Auth::user()->hasPermission('standard-texts.read'))
                            <a class="collapse-item" href="{{ route('std-texts.index') }}">{{ __('Std texts') }}</a>
                        @endif
                        @if (Auth::user()->hasPermission('interesting-websites.read'))
                            <a class="collapse-item" href="{{ route('interesting-websites.index') }}">{{ __('Interesting websites') }}</a>
                        @endif
                        @if (Auth::user()->hasPermission('interesting-websites.read'))
                            <a class="collapse-item" href="{{ route('our-links.index') }}">{{ __('Our links') }}</a>
                        @endif
                        @if (Auth::user()->hasPermission('general-documents.read'))
                            <a class="collapse-item" href="{{ route('offers-reservations-contracts.index') }}">{{ __('Offers, reservations and contracts') }}</a>
                        @endif
                        @if (Auth::user()->hasPermission('guidelines.read'))
                            <a class="collapse-item" href="{{ route('guidelines.index') }}">{{ __('Guidelines') }}</a>
                        @endif
                        @if (Auth::user()->hasPermission('guidelines.read'))
                            <a class="collapse-item" href="{{ route('protocols.index') }}">{{ __('Protocols') }}</a>
                        @endif
                        @if (Auth::user()->hasPermission('general-documents.read'))
                            <a class="collapse-item" href="{{ route('veterinary_documents.index') }}">{{ __('Veterinary documents') }}</a>
                        @endif
                        @if (Auth::user()->hasPermission('general-documents.read'))
                            <a class="collapse-item" href="{{ route('general_documents.index') }}">{{ __('General documents') }}</a>
                        @endif
                        <a class="collapse-item" href="{{ route('mailings.index') }}">{{ __('Mailings') }}</a>
                        <a class="collapse-item" href="{{ route('search-mailings.index') }}">{{ __('Search mailings') }}</a>
                        @if (Auth::user()->hasPermission('standard-texts.read'))
                            <a class="collapse-item" href="{{ route('website-texts.index') }}">{{ __('Website') }}</a>
                        @endif
                        @if (Auth::user()->hasPermission('standard-texts.read'))
                            <a class="collapse-item" href="{{ route('domain-name-link.index') }}">{{ __('Domain to name link') }}</a>
                        @endif
                    </div>
                </div>
            </li>
        @endif

        @if (Auth::user()->hasRole(['admin', 'transport', 'rossmery', 'office', 'readonly']))
            <li class="nav-item my-0">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSettings" aria-expanded="true" aria-controls="collapseSettings">
                    <i class="fas fa-fw fa-cogs"></i>
                    <span>{{ __('Master data') }}</span>
                </a>
                <div id="collapseSettings" class="collapse" aria-labelledby="headingSettings" data-parent="#accordionSidebar">
                    <div class="bg-white py-1 collapse-inner rounded">
                        <h6 class="collapse-header">Masterdata</h6>
                        @if (Auth::user()->hasRole(['admin']))
                            <a class="collapse-item" href="{{ route('dashboards.index') }}">{{ __('Dashboards') }}</a>
                        @endif
                        @if (Auth::user()->hasPermission('animals.read'))
                            <a class="collapse-item" href="{{ route('animals.index') }}">{{ __('Animals') }}</a>
                        @endif
                        <a class="collapse-item" href="{{ route('currencies.index') }}">{{ __('Currencies') }}</a>
                        @if (Auth::user()->hasRole(['admin', 'transport', 'rossmery']))
                            <a class="collapse-item" href="{{ route('countries.index') }}">{{ __('Countries') }}</a>
                            <a class="collapse-item" href="{{ route('regions.index') }}">{{ __('Regions') }}</a>
                            <a class="collapse-item" href="{{ route('areas.index') }}">{{ __('Areas') }}</a>
                            <a class="collapse-item" href="{{ route('airports.index') }}">{{ __('Airports') }}</a>
                            <a class="collapse-item" href="{{ route('origins.index') }}">{{ __('Origins') }}</a>
                        @endif
                        @if (Auth::user()->hasRole(['admin', 'transport', 'rossmery']))
                            <a class="collapse-item" href="{{ route('bank_accounts.index') }}">{{ __('Bank accounts') }}</a>
                            <a class="collapse-item" href="{{ route('basic-details.index') }}">{{ __('Basic details') }}</a>
                        @endif
                    </div>
                </div>
            </li>
        @endif

        @if (Auth::user()->hasRole('admin'))
            <li class="nav-item my-0">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUsers" aria-expanded="true" aria-controls="collapseUsers">
                    <i class="fas fa-fw fa-users"></i>
                    <span>{{ __('Users') }}</span>
                </a>
                <div id="collapseUsers" class="collapse" aria-labelledby="headingUsers" data-parent="#accordionSidebar">
                    <div class="bg-white py-1 collapse-inner rounded">
                        <h6 class="collapse-header">Users</h6>
                        <a class="collapse-item" href="{{ route('users.index') }}">{{ __('Users') }}</a>
                        <a class="collapse-item" href="{{ route('roles.index') }}">{{ __('Roles') }}</a>
                        <a class="collapse-item" href="{{ route('permissions.index') }}">{{ __('Permissions') }}</a>
                    </div>
                </div>
            </li>
        @endif

    </ul>
</div>
<!-- End of Sidebar -->
