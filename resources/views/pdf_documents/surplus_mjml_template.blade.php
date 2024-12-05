<mjml>
    <mj-head>
        <mj-attributes>
            @if ($document === 'html')
            <mj-all font-family="'Open Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif"></mj-all>
            @endif
            <mj-all padding="0" />
        </mj-attributes>
        <mj-breakpoint width="800px" />
        <mj-style>
            a.beige-link {
            text-decoration: none;
            color: #f1efd6;
            }
            .green-button a,
            .green-button a:link,
            .green-button a:visited,
            .green-button a:hover,
            .green-button u {
            color: #f1efd6 !important;
            text-decoration: none !important;
            }
            @media screen and (max-width:800px) {
            .desktop-lower-logo-part img {
            display: none !important;
            }
            .mobile-lower-logo-part img {
            display: block !important;
            }
            .center-on-mobile,
            .center-on-mobile > div {
            text-align: center !important;
            }
            .padding-on-mobile__medium {
            padding: 10px !important;
            }
            .padding-left-on-mobile__none,
            .padding-left-on-mobile__none td {
            padding-left: 0px !important;
            }
            .padding-left-on-mobile__medium,
            td.padding-left-on-mobile__medium,
            .padding-left-on-mobile__medium > div {
            padding-left: 10px !important;
            }
            .padding-left-on-mobile__large,
            td.padding-left-on-mobile__large,
            .padding-left-on-mobile__large > div {
            padding-left: 20px !important;
            }
            .padding-right-on-mobile__none,
            .padding-right-on-mobile__none td {
            padding-right: 0px !important;
            }
            .padding-right-on-mobile__medium,
            td.padding-right-on-mobile__medium {
            padding-right: 10px !important;
            }
            .padding-top-on-mobile__none {
            padding-top: 0px !important;
            }
            .padding-top-on-mobile__small {
            padding-top: 5px !important;
            }
            .padding-top-on-mobile__medium {
            padding-top: 10px !important;
            }
            .padding-top__medium {
            padding-top: 10px !important;
            }
            .padding-top-on-mobile__large {
            padding-top: 20px !important;
            }
            .padding-bottom-on-mobile__small {
            padding-bottom: 5px !important;
            }
            .padding-bottom-on-mobile__medium {
            padding-bottom: 10px !important;
            }
            .padding-bottom-on-mobile__large {
            padding-bottom: 20px !important;
            }
            }
        </mj-style>
        <mj-style inline="inline">
            .headerlink img,
            .green-button img {
            vertical-align: middle;
            }
            .white-background {
            background: #fff;
            }
            .beige-background {
            background: #f1efd6;
            }
            .textcolumn-list ul {
            margin-left: 0;
            padding-left: 20px;
            line-height: 24px;
            font-size: 14px;
            }
            .mobile-lower-logo-part img {
            display: none !important;
            }
            .bullet-list-table td {
            vertical-align: top;
            }
        </mj-style>
        @if (!$only_print_leadin && (($picture === 'yes' || $picture === "surplus")))
        <mj-raw>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css" integrity="sha512-ZKX+BvQihRJPA8CROKBhDNvoc2aDMOdAlcm7TUQY+35XYtrd3yh95QOOhsPDQY9QnKE0Wqag9y38OIgEvb88cA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        </mj-raw>
        @endif
        @if (!$only_print_leadin)
        <mj-raw>
            <!-- Google tag (gtag.js) -->
            <script async src="https://www.googletagmanager.com/gtag/js?id=G-ZSMFM1Y69P"></script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());

                gtag('config', 'G-ZSMFM1Y69P');
            </script>
            <title>{{ $name }}</title>
        </mj-raw>
        @endif
    </mj-head>
    <mj-body width="800px" background-color="#ffffff">
        <!-- Start header -->
        <mj-section full-width="full-width">
            <mj-column width="25%">
                <mj-image src="https://www-tst.zoo-services.com/images/bovenkant-logo.jpg" alt="Top part of logo" />
                <mj-raw>
                    <!--[if !mso]><!-->
                </mj-raw>
                <mj-image src="https://www-tst.zoo-services.com/images/onderkant-logo-mobiel.jpg" alt="Desktop bottom lart of logo" css-class="mobile-lower-logo-part" />
                <mj-raw>
                    <!--<![endif]-->
                </mj-raw>
            </mj-column>
            <mj-column width="75%">
                <mj-text align="right" font-size="28px" padding="18px 0 0 0" css-class="center-on-mobile padding-top-on-mobile__small padding-bottom-on-mobile__medium">
                    INTERNATIONAL ZOO SERVICES
                </mj-text>
                <mj-text align="right" font-size="14px" padding="10px 0 0 0" css-class="center-on-mobile padding-top-on-mobile__small padding-bottom-on-mobile__medium">
                    The Hague, Netherlands
                </mj-text>
            </mj-column>
        </mj-section>
        <mj-section full-width="full-width" background-color="#6d9c4d">
            <mj-column width="25%">
                <mj-image src="https://www-tst.zoo-services.com/images/onderkant-logo.jpg" alt="Desktop bottom lart of logo" css-class="desktop-lower-logo-part" />
            </mj-column>
            <mj-column width="75%">
                <mj-text align="right" font-weight="800" css-class="headerlink" padding="14px 0 0 0" font-size="13px">
                    <a href="https://www.zoo-services.com" class="beige-link">
                        www.zoo-services.com
                    </a>
                    <img src="https://www-tst.zoo-services.com/images/pijl-rechts.jpg" alt=">" width="14" height="14" />
                </mj-text>
                <mj-text align="right" font-weight="800" css-class="headerlink" padding="5px 0 14px 0" font-size="13px">
                    <a href="mailto:info@zoo-services.com" class="beige-link">
                        info@zoo-services.com
                    </a>
                    <img src="https://www-tst.zoo-services.com/images/pijl-rechts.jpg" alt=">" width="14" height="14" />
                </mj-text>
            </mj-column>
        </mj-section>
        <!-- End header -->
        <!-- Start surplus lead-in -->
        @if (!$only_print_leadin)
        <mj-section background-color="#f1efd6" full-width="full-width">
            <mj-column width="100%" padding="25px 0 5px 0">
                <mj-text css-class="padding-left-on-mobile__medium">
                    {{ $date }}
                </mj-text>
            </mj-column>
            <mj-column background-color="#936920" width="100%">
                <mj-text color="#ffffff" font-size="24px" font-weight="800" align="center" padding="10px 0" css-class="padding-left-on-mobile__medium">
                    @if ($language === 'english')
                        SURPLUS LIST - available species
                    @else
                        Lista de excedentes
                    @endif
                </mj-text>
            </mj-column>
            <mj-column width="100%" padding="10px 0 25px 0">
                <mj-text color="#000000" font-size="16px" font-weight="600" padding="6px 0" css-class="padding-left-on-mobile__medium padding-right-on-mobile__medium">
                    @if ($language === 'english')
                        Following species are surplus to the collections of our relations;
                        For more information please contact us at:
                    @else
                        Las siguientes especies son excedentes para las colecciones
                        de nuestras relaciones; Para más información por favor contáctenos en:
                    @endif
                    <a href="mailto:info@zoo-services.com">info@zoo-services.com</a>
                </mj-text>
            </mj-column>
        </mj-section>
        <mj-section background-color="#f1efd6" full-width="full-width">
            <mj-column width="100%" background-color="#6d9c4d" padding="1px 0">
            </mj-column>
        </mj-section>
        <mj-section background-color="#f1efd6" full-width="full-width">
            <mj-column width="100%" padding="20px 0 20px 0">
                <mj-text font-size="14px" line-height="17px" css-class="padding-left-on-mobile__medium padding-right-on-mobile__medium">
                    @if ($language === 'english')
                        @if (!empty($print_stuffed) && $print_stuffed === 'yes')
                            Besides live animals, International Zoo Services, also supplies
                            taxidermy material for educational purposes.<br>
                            For zoos as well museums. We ship worldwide. In case prices are
                            mentioned, please note that these are without transportation costs, etc.<br>
                            Here below a survey of stuffed specimens that are available.
                            For more information, please contact us at:
                            <b>
                                <a href="mailto:info@zoo-services.com" id="mail">info@zoo-services.com</a>
                                or via
                                <a href=" https://api.whatsapp.com/send?phone=0031854011610&text=Contact%20International%20Zoo%20Services">
                                    WhatsApp
                                </a>
                            </b>
                        @else
                            Explanation of abbreviations: Numbers and sexes are indicated as follows:<br>
                            <b>M</b>= Male <b>F</b>= Female <b>U</b>= Sex unknown <b>P</b>= Pair
                            <b>c.b.</b> = captive bred, <b>w.c.</b> = wild caught, <b>c.b/w.c</b>
                            = mixed group of captive bred and wild caught.
                            Ex "<b>continent</b>"= the continent from where the animals
                            are shipped.<br>
                            In case prices are mentioned, these are excluding crate and transport.
                        @endif
                    @else
                        @if (!empty($print_stuffed) && $print_stuffed === 'yes')
                            Además de animales vivos, International Zoo Services también suministra
                            material de taxidermia con fines educativos. Tanto para zoológicos como
                            para museos. Enviamos a todo el mundo. En caso de que se mencionen precios,
                            tenga en cuenta que estos no incluyen costos de transporte, etc.<br><br>
                            A continuación se muestra un repaso de los ejemplares disecados que se
                            encuentran disponibles.<br><br>
                            Para obtener más información, póngase en contacto con nosotros en:
                            <b>
                                <a href="mailto:info@zoo-services.com" id="mail">info@zoo-services.com</a>
                            </b>
                        @else
                            Las especies que se mencionan a continuaci&oacute;n son excedentes de las
                            colecciones de nuestros proveedores. Si desea m&aacute;s informaci&oacute;n,
                            puede solicitarla a nuestro email:
                                <b>
                                    <a href="mailto:info@zoo-services.com" id="mail">
                                        info@zoo-services.com
                                    </a>.
                                    En caso de que se mencionen precios, tenga en cuenta que estos no
                                    incluyen costos de transporte, costos veterinarios, etc.
                                </b>
                            <br><br>
                            Explicaci&oacute;n de las abreviaciones: La cantidad seg&uacute;n sexo se
                            indica como sigue:<br>
                            @if (!$is_standard)
                                M-F-U-Pr: M = cantidad de machos, F = cantidad de hembras, U = cantidad no sexados,
                                Pr = pareja, x = numerosos espec&iacute;menes disponibles.<br>
                            @endif
                            c.b. = nacido en cautiverio, w.c. = capturado salvaje, c.b/w.c = grupo mixto.<br>
                            Ex &quot;continente&quot;= continente de origen de donde serán enviados los
                            animales.
                        @endif
                    @endif
                </mj-text>
            </mj-column>
        </mj-section>
        @else
        <!-- Start section for when only printing lead-in -->
        <mj-section background-color="#f1efd6" full-width="full-width">
            <mj-column width="100%" padding="20px 0 20px 0">
                <mj-text font-size="16px" line-height="21px" css-class="padding-left-on-mobile__medium padding-right-on-mobile__medium">
                    @if ($language === 'english')
                        Dear @{{ contact.NOMBRE }},<br>
                        Below please find an introduction to our current list
                        of surplus. To see the entire list of what is currently
                        available, click the 'More available species' button below.<br><br>
                        For any questions you can contact us at:
                        <a href="mailto:info@zoo-services.com">info@zoo-services.com</a>
                        or via
                        <a href="https://api.whatsapp.com/send?phone=0031854011610&text=Contact%20International%20Zoo%20Services">
                            WhatsApp
                        </a>
                    @else
                        Estimado/a @{{ contact.NOMBRE }},<br>
                        A continuación encontrará una muestra de nuestra lista actual de
                        excedentes. Para ver la lista completa de las especies disponible
                        actualmente, haga clic en el botón "Más especies disponibles"
                        a continuación.<br><br>
                        Para cualquier consulta puede contactar con nosotros en
                        <a href="mailto:info@zoo-services.com">info@zoo-services.com</a>
                        or via
                        <a href="https://api.whatsapp.com/send?phone=0031854011610&text=Contact%20International%20Zoo%20Services">
                            WhatsApp
                        </a>
                    @endif
                </mj-text>
                <mj-button
                    align="left"
                    background-color="#6d9c4d"
                    padding="10px 0 0 0"
                    color="#f1efd6"
                    font-weight="600"
                    css-class="green-button padding-left-on-mobile__large"
                    border-radius="5px"
                    font-size="12px"
                    href="{{ $download_url }}?utm_source=surplus_mailing&utm_medium=email&utm_campaign={{ $name }}"
                >
                    @if ($language === 'english')
                        More available species
                    @else
                        Más especies disponibles
                    @endif
                    <img
                        src="https://www-tst.zoo-services.com/images/pijl-rechts.jpg"
                        alt=">"
                        width="14"
                        height="14"
                    />
                </mj-button>
            </mj-column>
        </mj-section>
        <!-- End section for when only printing lead-in -->
        @endif
        <!-- End surplus lead-in -->
        <mj-section background-color="#ffffff" padding="5px 0">
        </mj-section>
        {{--@if (!$only_print_leadin)--}} {{-- Start conditioning entire list --}}
        <!-- Start surplus list -->
        @foreach ($surplusToPrint as $class => $surplusClass)
        @if (!$only_print_leadin) {{-- Don't show class header in e-mail --}}
        <!-- Start class header -->
        <mj-section>
            <mj-column width="100%" background-color="#f1efd6" padding="5px 0 5px 15px">
                <mj-text color="#6d9c4d" font-size="21px" font-weight="400" padding="6px 0">
                    {{ strtoupper($class) }}
                </mj-text>
            </mj-column>
        </mj-section>
        <!-- End class header -->
        @endif
        @foreach ($surplusClass as $order => $surplusOrder)
        @if ($loop->iteration < $row_limit)
        @if (!$only_print_leadin) {{-- Don't show order header in e-mail --}}
        <!-- Start order header -->
        <mj-section>
            <mj-column width="100%" background-color="#6d9c4d" padding="8px 0 8px 125px">
                <mj-text color="#ffffff" font-weight="600" font-size="15px">
                    {{ $order }}
                </mj-text>
            </mj-column>
        </mj-section>
        <!-- End order header -->
        @endif
        @foreach ($surplusOrder as $surplus)
            @php $base_url = url('/'); @endphp
            @php $img_src  = '/storage/animals_pictures/image_not_available.png'; @endphp
            @if((!empty($surplus->catalog_pic) && $picture === 'surplus') || (!empty($print_stuffed) && $print_stuffed === 'yes'))
                @php $img_src = '/storage/surpluses_pictures/' . $surplus->id . '/' . $surplus->catalog_pic; @endphp
            @elseif ($surplus->animal->catalog_pic !== null && Storage::exists('public/animals_pictures/' . $surplus->animal->id . '/' . $surplus->animal->catalog_pic))
                @php $img_src = '/storage/animals_pictures/' . $surplus->animal->id . '/' . $surplus->animal->catalog_pic; @endphp
            @endif
        <!-- Start species row -->
        <mj-section padding="0 0 5px 0">
            <mj-column width="100%">
                <mj-table>
                    <tr>
                        @if ($picture === 'yes' || $picture === "surplus")
                        <td style="width: 120px; padding: 0; vertical-align: top;">
                            <table role="presentation" style="border-collapse:collapse;border-spacing:0px;" cellspacing="0" cellpadding="0" border="0">
                                <tbody>
                                    <tr>
                                        <td style="width:120px;">
                                            @if (!$only_print_leadin)
                                            <a href="{{ $base_url }}{{ $img_src }}" data-lightbox="{{ $surplus->id }}">
                                            @endif
                                                <img
                                                    alt="{{ $surplus->animal->scientific_name }}"
                                                    src="{{ $base_url }}{{ $img_src }}"
                                                    style="border:0;display:block;outline:none;text-decoration:none;height:auto;width:100%;font-size:13px;"
                                                    width="120"
                                                    height="auto"
                                                >
                                            @if (!$only_print_leadin)
                                            </a>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                        @endif
                        <td style="vertical-align: top;@if ($has_prices)width: 50%;@endif min-width: 215px;">
                            <table role="presentation" style="line-height: 18px; border-collapse:collapse;border-spacing:0px;" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td style="
                                            padding: 5px 0px 0px 0px;
                                            color: #000000;
                                            font-size: 13px;
                                            background-color: #f9f9ef;
                                            width: 30px;
                                            text-align: center;
                                            vertical-align: top;
                                        ">
                                        <span style="font-weight: 800;">M</span><br>
                                        {{ ($surplus->quantityM === -1 ? 'x' : $surplus->quantityM) ?? 0 }}
                                    </td>
                                    <td style="
                                            padding: 5px 0px 0px 0px;
                                            color: #000000;
                                            font-size: 13px;
                                            background-color: #fffffff;
                                            width: 30px;
                                            text-align: center;
                                            vertical-align: top;
                                        ">
                                        <span style="font-weight: 800;">F</span><br>
                                        {{ ($surplus->quantityF === -1 ? 'x' : $surplus->quantityF) ?? 0 }}
                                    </td>
                                    <td style="
                                            padding: 5px 0px 0px 0px;
                                            color: #000000;
                                            font-size: 13px;
                                            background-color: #f9f9ef;
                                            width: 30px;
                                            text-align: center;
                                            vertical-align: top;
                                        ">
                                        <span style="font-weight: 800;">U</span><br>
                                        {{ ($surplus->quantityU === -1 ? 'x' : $surplus->quantityU) ?? 0 }}
                                    </td>
                                    <td style="
                                            padding: 5px 0px 0px 0px;
                                            color: #000000;
                                            font-size: 13px;
                                            background-color: #fffffff;
                                            width: 30px;
                                            text-align: center;
                                            vertical-align: top;
                                        ">
                                        <span style="font-weight: 800;">P</span><br>
                                        {{ ($surplus->quantityP === -1 ? 'x' : $surplus->quantityP) ?? 0 }}
                                    </td>
                                    <td style="
                                            padding: 5px 3px 0px 7px;
                                            color: #000000;
                                            font-size: 14px;
                                            background-color: #fffffff;
                                            line-height: 18px;
                                            vertical-align: top;
                                            font-weight: 800;
                                        ">
                                        {{ ($language === 'spanish' && $surplus->animal->spanish_name)
                                            ? $surplus->animal->spanish_name
                                            : $surplus->animal->common_name
                                        }}
                                        <br>
                                        <span style="font-size: 11px; font-style: italic;">{{ $surplus->animal->scientific_name }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5" style="padding: 0px 0px 5px 10px;">
                                        <span style="font-size: 11px;">
                                            @if (isset($surplus->age_field) && $surplus->age_field !== '')
                                                {{ $surplus->age_field }}
                                            @endif
                                            @if (isset($surplus->bornYear))
                                                {{ $surplus->bornYear }}
                                            @endif
                                            {{ $surplus->list_remarks }}
                                            <br>
                                            {{ $surplus->location }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        @if ($has_prices)
                            <td style="
                                    padding: 5px 0px 5px 0px;
                                    color: #000000;
                                    font-size: 13px;
                                    background-color: #fffffff;
                                    vertical-align: top;
                                    min-width: 140px;
                                ">
                                <table style="border-collapse:collapse;border-spacing:0px;" cellspacing="0" cellpadding="0" border="0">
                                    <tr>
                                        <td style="
                                                font-size: 11px;
                                                font-weight: 800;
                                            ">Currency:</td>
                                        <td style="
                                                padding-left: 5px;
                                        ">{{ $surplus->sale_currency }}</td>
                                    </tr>
                                    @if ($surplus->quantityM !== 0)
                                    <tr>
                                        <td style="
                                                font-size: 11px;
                                                font-weight: 800;
                                            ">Each male:</td>
                                        <td style="
                                                padding-left: 5px;
                                        ">{{ number_format($surplus->salePriceM, 2, '.', '') }}</td>
                                    </tr>
                                    @endif
                                    @if ($surplus->quantityF !== 0)
                                    <tr>
                                        <td style="
                                                font-size: 11px;
                                                font-weight: 800;
                                            ">Each female:</td>
                                        <td style="
                                                padding-left: 5px;
                                        ">{{ number_format($surplus->salePriceF, 2, '.', '') }}</td>
                                    </tr>
                                    @endif
                                    @if ($surplus->quantityU !== 0)
                                    <tr>
                                        <td style="
                                                font-size: 11px;
                                                font-weight: 800;
                                            ">Each unknown</td>
                                        <td style="
                                                padding-left: 5px;
                                        ">{{ number_format($surplus->salePriceU, 2, '.', '') }}</td>
                                    </tr>
                                    @endif
                                    @if ($surplus->quantityP !== 0)
                                    <tr>
                                        <td style="
                                                font-size: 11px;
                                                font-weight: 800;
                                            ">Each pair</td>
                                        <td style="
                                                padding-left: 5px;
                                        ">{{ number_format($surplus->salePriceP, 2, '.', '') }}</td>
                                    </tr>
                                    @endif
                                </table>
                            </td>
                        @endif
                    </tr>
                </mj-table>
            </mj-column>
        </mj-section>
        <!-- End species row -->
        @endforeach {{-- End loop of surplusspecimens --}}
        </mj-section>
        <!-- End surplus list -->
        @endif
        @if (!$only_print_leadin)
        <mj-section>
            <mj-column width="100%" background-color="#f1efd6" padding="5px 0 5px 5px">
                <mj-text color="#000000" font-size="13px" font-weight="400" padding="6px 0">
                    <b>For information please contact us at <a href="mailto:info@zoo-services.com">info@zoo-services.com</a></b>
                </mj-text>
            </mj-column>
            <mj-column width="100%" background-color="#ffffff" padding="5px 0">
            </mj-column>
        </mj-section>
        @endif
        @endforeach {{-- End loop of surplusorders --}}
        @endforeach {{-- End loop of surplusclasses --}}
        {{-- In the summary version, print a button
             to the entire list under the classes --}}
        @if ($only_print_leadin)
        <mj-section full-width="full-width">
            <mj-column width="100%" padding="10px 0 10px 0">
                <mj-button align="left" background-color="#6d9c4d" padding="0" color="#f1efd6" font-weight="600" css-class="green-button" border-radius="5px" font-size="12px" href="{{ $download_url }}?utm_source=surplus_mailing&utm_medium=email&utm_campaign={{ $name }}">

                    @if ($language === 'english')
                    More available species
                    @else
                    Más especies disponibles
                    @endif
                    <img src="https://www-tst.zoo-services.com/images/pijl-rechts.jpg" alt=">" width="14" height="14" />
                </mj-button>
            </mj-column>
        </mj-section>
        @endif
        @if (isset($wanted) && $wanted !== false)
        <!-- Start wanted lead-in -->
        <mj-section background-color="#f1efd6" full-width="full-width">
            <mj-column width="100%" padding="25px 0 5px 0">
                <mj-text css-class="padding-left-on-mobile__medium padding-right-on-mobile__medium">
                    {{ $date }}
                </mj-text>
            </mj-column>
            <mj-column background-color="#936920" width="100%">
                <mj-text color="#ffffff" font-size="24px" font-weight="800" align="center" padding="10px 0" css-class="padding-left-on-mobile__medium padding-right-on-mobile__medium">

                    @if ($language === 'english')
                        WANTED: we are looking for
                    @else
                        Lista de buscados
                    @endif
                </mj-text>
            </mj-column>
            <mj-column width="100%" padding="10px 0 25px 0">
                <mj-text color="#000000" font-size="16px" font-weight="600" padding="6px 0" css-class="padding-left-on-mobile__medium padding-right-on-mobile__medium">
                    @if ($language === 'english')
                        We received inquiries from our relations for following species.<br><br>
                        Please note that on request all details about the future destination
                        can be provided. For information please contact us at
                        <a href="mailto:info@zoo-services.com">info@zoo-services.com</a>
                    @else
                        Hemos recibido consultas de nuestras relaciones para las siguientes especies.
                        Por favor, haganos saber si usted tiene una o mas de estas especies disponibles;
                        tenga en cuenta que en la solicitud, todos los detalles sobre el destino de
                        la especie pueden ser proporcionados. Para mas informacion, por favor contactenos a
                        <a href="mailto:info@zoo-services.com">info@zoo-services.com</a>
                    @endif
                </mj-text>
            </mj-column>
        </mj-section>
        <mj-section background-color="#f1efd6" full-width="full-width">
            <mj-column width="100%" background-color="#6d9c4d" padding="1px 0">
            </mj-column>
        </mj-section>
        <mj-section background-color="#f1efd6" full-width="full-width">
            <mj-column width="100%" padding="15px 0 20px 0">
                <mj-text font-size="14px" line-height="23px" css-class="padding-left-on-mobile__medium padding-right-on-mobile__medium">
                    @if ($language === 'english')
                        Explanation of abbreviations:<br><b>c.b.</b> = captive
                        bred, <b>w.c.</b>= wild caught, <b>c.b./w.c.</b> = mixed group of captive bred and wild caught.
                    @else
                        Explicaci&oacute;n de las abreviaciones: La cantidad seg&uacute;n sexo se
                        indica como sigue:<br>
                        c.b. = nacido en cautiverio, w.c. = capturado salvaje, c.b/w.c = grupo mixto.<br>
                        Ex &quot;continente&quot;= continente de origen de donde serán enviados los
                        animales.
                    @endif
                </mj-text>
            </mj-column>
        </mj-section>
        <!-- End wanted lead-in -->
        <mj-section background-color="#ffffff" padding="5px 0">
        </mj-section>
        <!-- Start wanted list -->
        @foreach ($wantedToPrint as $class => $wantedClass)
        <!-- Start class header -->
        <mj-section>
            <mj-column width="100%" background-color="#f1efd6" padding="5px 0 5px 15px">
                <mj-text color="#6d9c4d" font-size="21px" font-weight="400" padding="6px 0">
                    {{ $class }}
                </mj-text>
            </mj-column>
        </mj-section>
        <mj-section>
            <mj-column width="100%">
                <mj-table>
                    <tr>
                        @if ($picture === 'yes')
                        <td style="
                                padding: 5px 0px 5px 5px;
                                color: #6d9c4d;
                                font-size: 14px;
                                width: 34%;
                                line-height: 18px;
                                background-color: #fffffff;
                                width: 120px;
                            ">Image</td>
                        @endif
                        <td style="
                                padding: 5px 0px 5px 5px;
                                color: #6d9c4d;
                                font-size: 14px;
                                background-color: #fffffff;
                                width: 34%;
                                line-height: 18px;
                            ">Name</td>
                        <td style="
                                padding: 5px 0px 5px 0px;
                                color: #6d9c4d;
                                font-size: 14px;
                                background-color: #fffffff;
                            ">Remarks</td>
                    </tr>
                </mj-table>
            </mj-column>
        </mj-section>
        <!-- End class header -->
        @foreach ($wantedClass as $order => $wantedOrder)
        <!-- Start order header -->
        <mj-section>
            <mj-column width="100%" background-color="#6d9c4d" padding="8px 0 8px 125px">
                <mj-text color="#ffffff" font-weight="600" font-size="15px">
                    {{ $order }}
                </mj-text>
            </mj-column>
        </mj-section>
        <!-- End order header -->
        @foreach ($wantedOrder as $wanted)
        @php $img_src = '/storage/animals_pictures/' . $wanted->animal->id . '/' . $wanted->animal->catalog_pic; @endphp
        <!-- Start species row -->
        <mj-section padding="0 0 5px 0">
            <mj-column width="100%">
                <mj-table>
                    <tr>
                        @if ($picture === 'yes')
                        <td style="width: 120px; padding: 0; vertical-align: top;">
                            <table role="presentation" style="border-collapse:collapse;border-spacing:0px;" cellspacing="0" cellpadding="0" border="0">
                                <tbody>
                                    <tr>
                                        <td style="width:120px;">
                                            <a href="{{ $base_url }}{{ $img_src }}" data-lightbox="{{ $wanted->id }}">
                                                <img alt="{{ $wanted->animal->scientific_name }}" src="{{ $base_url }}{{ $img_src }}" style="border:0;display:block;outline:none;text-decoration:none;height:auto;width:100%;font-size:13px;" width="120" height="auto">
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                        @endif
                        <td style="
                                padding: 5px 3px 5px 7px;
                                color: #000000;
                                font-size: 13px;
                                font-weight: 800;
                                background-color: #fffffff;
                                width: 34%;
                                line-height: 18px;
                                vertical-align: top;
                            ">
                                {{ ($language === 'spanish' && $wanted->animal->spanish_name)
                                    ? $wanted->animal->spanish_name
                                    : $wanted->animal->common_name
                                }}
                                <br>
                                <span style="font-style: italic;">{{ $wanted->animal->scientific_name }}</span>
                            </td>
                        <td style="
                                padding: 5px 0px 5px 0px;
                                color: #000000;
                                font-size: 13px;
                                background-color: #fffffff;
                                vertical-align: top;

                            ">
                                {{ ($language == 'spanish') ? __('es.'.$wanted->looking_for) : $wanted->looking_field }}<br>
                                @if ($wanted->age_field !== '')
                                    {{ $wanted->age_field }}<br>
                                @endif
                                @if ($wanted->origin_field !== '')
                                    {{ $wanted->origin_field }}<br>
                                @endif
                                {{ $wanted->remarks }}
                            </td>
                    </tr>
                </mj-table>
            </mj-column>
        </mj-section>
        <!-- End species row -->
        @endforeach {{-- End wanted species loop --}}
        @endforeach {{-- End wanted order loop --}}
        <mj-section>
            <mj-column width="100%" background-color="#f1efd6" padding="5px 0 5px 5px">
                <mj-text color="#000000" font-size="13px" font-weight="400" padding="6px 0">
                    <b>For information please contact us at <a href="mailto:info@zoo-services.com">info@zoo-services.com</a></b>
                </mj-text>
            </mj-column>
            <mj-column width="100%" background-color="#ffffff" padding="5px 0">
            </mj-column>
        </mj-section>
        @endforeach {{-- End main wanted class loop --}}
        <!-- End wanted list -->
        @endif {{-- End 'if' that conditions wanted section --}}
        @if (!$only_print_leadin)
        <!-- Start beige spacer -->
        <mj-section padding="40px 0 0 0">
            <mj-column width="100%" background-color="#f1efd6" padding="1px 0">
            </mj-column>
        </mj-section>
        <!-- End beige spacer -->
        @endif
        <!-- Start left textcolumn -->
        <mj-section>
            <mj-column width="50%" padding="0 15px 0 0" css-class="padding-right-on-mobile__none">
                <mj-text color="#6d9c4d" font-size="19px" font-weight="400" padding="10px 0 5px 10px" css-class="beige-background padding-left-on-mobile__medium">
                    @if ($language === 'english')EXCHANGE @else INTERCAMBIO @endif
                </mj-text>
                <mj-text color="#565457" font-size="16px" font-weight="600" padding="0 0 10px 10px" css-class="beige-background padding-left-on-mobile__medium">
                    @if ($language === 'english')Our definition @else Nuestra definición @endif
                </mj-text>
                <mj-text css-class="white-background padding-left-on-mobile__medium" padding="10px" font-size="14px" line-height="24px">
                    @if ($language === 'english')
                        We can consider supplying animals in exchange for your surplus animals;
                        Details of the future destination can be provided on request.
                        Please send us your surplus list to:
                    @else
                        Podemos considerar suministrar animales a cambio de sus animales
                        excedentes; los detalles de las instalaciones de destino se proveerán
                        si así lo requiriese su institución. Por favor envíenos su lista
                        de excedentes a:
                    @endif
                </mj-text>
                <mj-button align="left" background-color="#6d9c4d" padding="10px 0 0 10px" color="#f1efd6" font-weight="600" css-class="green-button padding-left-on-mobile__large" border-radius="5px" font-size="12px" href="mailto:info@zoo-services.com">
                    info@zoo-services.com
                    <img src="https://www-tst.zoo-services.com/images/pijl-rechts.jpg" alt=">" width="14" height="14" />
                </mj-button>
            </mj-column>
            <!-- End left textcolumn -->
            <!-- Start right textcolumn -->
            <mj-column width="50%" padding="0 0 0 15px" css-class="padding-left-on-mobile__none padding-top-on-mobile__large">
                <mj-text color="#6d9c4d" font-size="19px" font-weight="400" padding="10px 0 5px 10px" css-class="beige-background padding-left-on-mobile__medium">
                    @if ($language === 'english')AVAILABLE SPECIES @else ESPECIES DISPONIBLES @endif
                </mj-text>
                <mj-text color="#565457" font-size="16px" font-weight="600" padding="0 0 10px 10px" css-class="beige-background padding-left-on-mobile__medium">
                    @if ($language === 'english')What we offer: @else Lo que ofrecemos: @endif
                </mj-text>
                <mj-text css-class="white-background padding-left-on-mobile__medium" padding="10px" font-size="14px" line-height="24px">
                    @if ($language === 'english')
                        We offer a large variety of species; Thousands of specimens
                        that are surplus to the collections of over 4000 zoological
                        institutions worldwide are registered in our inventory.
                    @else
                        Ofrecemos una gran variedad de especies; en nuestro inventario
                        están registrados miles de especímenes excedentes de las
                        colecciones de más de 4000 instituciones zoológicas de todo el mundo.
                    @endif
                </mj-text>
                <mj-button align="left" background-color="#6d9c4d" padding="10px 0 0 10px" color="#f1efd6" font-weight="600" css-class="green-button padding-left-on-mobile__large" border-radius="5px" font-size="12px" href="https://www.zoo-services.com">
                    @if ($language === 'english')Our complete inventory @else Nuestro inventario completo @endif
                    <img src="https://www-tst.zoo-services.com/images/pijl-rechts.jpg" alt=">" width="14" height="14" />
                </mj-button>
            </mj-column>
        </mj-section>
        <!-- End right textcolumn -->
        <!-- Start left textcolumn -->
        <mj-section padding="0 0 40px 0">
            <mj-column width="50%" padding="30px 15px 0 0" css-class="padding-right-on-mobile__none">
                <mj-text color="#6d9c4d" font-size="19px" font-weight="400" padding="10px 0 5px 10px" css-class="beige-background padding-left-on-mobile__medium">
                    @if ($language === 'english')ABOUT US @else SOBRE NOSOTROS @endif
                </mj-text>
                <mj-text color="#565457" font-size="16px" font-weight="600" padding="0 0 10px 10px" css-class="beige-background padding-left-on-mobile__medium">
                    @if ($language === 'english')Who we are @else Quiénes somos @endif
                </mj-text>
                <mj-text css-class="white-background padding-left-on-mobile__medium" padding="10px" font-size="14px" line-height="24px">
                    @if ($language === 'english')
                        International Zoo Services is a Dutch organization established in 1985
                        offering consultancy concerning the supply of animals to zoos and well-
                        recognized private breeding centers worldwide.
                    @else
                        International Zoo Services es una organización holandesa fundada
                        en 1985 que ofrece consultoría sobre el suministro de animales a
                        zoológicos y centros privados de cría especializados y de
                        reconocimiento mundial.
                    @endif
                </mj-text>
                <mj-button align="left" background-color="#6d9c4d" padding="10px 0 0 10px" color="#f1efd6" font-weight="600" css-class="green-button padding-left-on-mobile__large" border-radius="5px" font-size="12px" href="https://www.zoo-services.com/about-us">
                    @if ($language === 'english') More information @else Más información @endif
                    <img src="https://www-tst.zoo-services.com/images/pijl-rechts.jpg" alt=">" width="14" height="14" />
                </mj-button>
            </mj-column>
            <mj-column width="50%" padding="30px 0 0 15px" css-class="padding-left-on-mobile__none">
                <mj-text color="#6d9c4d" font-size="19px" font-weight="400" padding="10px 0 5px 10px" css-class="beige-background padding-left-on-mobile__medium">
                    @if ($language === 'english')Access to information @else Acceso a la informaci&oacute;n @endif
                </mj-text>
                <mj-text color="#565457" font-size="16px" font-weight="600" padding="0 0 10px 10px" css-class="beige-background padding-left-on-mobile__medium">
                    @if ($language === 'english')Keep up to date: @else Mantenerse informado @endif
                </mj-text>
                <mj-text css-class="white-background padding-left-on-mobile__medium" padding="10px" font-size="14px" line-height="24px">
                    @if ($language === 'english')
                        If you want to receive regularly special information about available species, please register yourself here:
                    @else
                        Si desea recibir periódicamente información detallada sobre las especies disponibles, regístrese aquí
                    @endif
                </mj-text>
                <mj-button align="left" background-color="#6d9c4d" padding="10px 0 0 10px" color="#f1efd6" font-weight="600" css-class="green-button padding-left-on-mobile__large" border-radius="5px" font-size="12px" href="https://www.zoo-services.com/register">
                    @if ($language === 'english')Receive information @else Recibir información @endif
                    <img src="https://www-tst.zoo-services.com/images/pijl-rechts.jpg" alt=">" width="14" height="14" />
                </mj-button>
            </mj-column>
            <!-- End right textcolumn -->
        </mj-section>
        <!-- End textcolumns section -->
        {{--@endif--}} {{-- Ends 'if' block that blocks the whole list when printing only leadin --}}
        <!-- Start footer -->
        <mj-section full-width="full-width" background-color="#000">
            <mj-column width="100%" padding="20px 0">
                <mj-text color="#6d9c4d" align="center" font-weight="600">
                    &copy; {{ date("Y", strtotime("now")) }} International Zoo Services
                </mj-text>
                <mj-text color="#6d9c4d" align="center" font-weight="600">
                    <a style="color: #6d9c4d" href=" https://api.whatsapp.com/send?phone=0031854011610&text=Contact%20International%20Zoo%20Services">Send us a WhatsApp</a>
                </mj-text>
                <mj-text color="#ffffff" text-decoration="underline" align="center" padding="5px 0 0 0" font-weight="600">
                    <a href="@{{ unsubscribe }}" title="Click here to unsubscribe" style="
                            color: #ffffff;
                        ">
                        @if ($only_print_leadin)
                            @if ($language === 'english')
                                Unsubscribe? click here
                            @else
                                ¿Anular la suscripción? Haga clic aquí
                            @endif
                        @endif
                    </a>
                </mj-text>
            </mj-column>
        </mj-section>
        <!-- End footer -->
        @if (!$only_print_leadin && ($picture === 'yes' || $picture === "surplus"))
        <mj-raw>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js" integrity="sha512-Ixzuzfxv1EqafeQlTCufWfaC6ful6WFqIz4G+dWvK0beHw0NVJwvCKSgafpy5gwNqKmgUfIBraVwkKI+Cz0SEQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        </mj-raw>
        @endif
    </mj-body>
</mjml>