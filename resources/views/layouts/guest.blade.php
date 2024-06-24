<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'PixelPartner') }}</title>

        <link rel="icon" type="image/x-icon" href="{{ asset('/storage/images/favicon.svg') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
            <div>
                <a href="/" class="w-20 h-20 fill-black dark:fill-white text-gray-500">
                    <svg width="208" height="97" viewBox="0 0 208 97" fill="currentCollor" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M70.8818 23.0691H86.1869L89.9135 26.5738V34.5172L86.1454 38.0527H75.6211V45.7723H70.8818V23.0768V23.0691ZM84.0206 34.3551L85.2407 33.2203V27.9015L84.0206 26.7667H75.6211V34.3551H84.0206Z"
                            fill="currentCollor" />
                        <path
                            d="M92.9854 26.2593V24.1837L94.6016 22.6946H97.0636L98.6799 24.1837V26.2593L97.0636 27.7484H94.6016L92.9854 26.2593ZM93.5129 29.631H98.1523V45.7724H93.5129V29.631Z"
                            fill="currentCollor" />
                        <path
                            d="M113.715 45.7723L109.881 40.8137L106.047 45.7723H100.853L107.334 37.5105L101.027 29.5129H106.222L109.881 34.2773L113.541 29.5129H118.736L112.462 37.5727L118.91 45.7645H113.715V45.7723Z"
                            fill="currentCollor" />
                        <path
                            d="M120.109 42.4319V32.9077L123.702 29.5129H133.492L137.118 32.9077V39.0759H124.714V41.1656L125.826 42.2454H131.509L132.555 41.2355V40.3888H137.085V42.5096L133.633 45.7723H123.636L120.118 42.4474L120.109 42.4319ZM132.513 36.0695V34.174L131.327 33.032H125.892L124.706 34.174V36.0695H132.513Z"
                            fill="currentCollor" />
                        <path d="M141.09 22.6943H145.586V45.7721H141.09V22.6943Z" fill="currentCollor" />
                        <path
                            d="M70.8818 51.467H86.1869L89.9135 54.9833V62.9529L86.1454 66.5001H75.6211V74.2451H70.8818V51.4748V51.467ZM84.0206 62.7903L85.2407 61.6517V56.3154L84.0206 55.1769H75.6211V62.7903H84.0206Z"
                            fill="currentCollor" />
                        <path
                            d="M91.8706 71.2649V67.5764L95.0891 64.604H103.952V62.5633L102.868 61.5622H97.6154L96.5648 62.5633V63.472H92.0124V61.6315L95.7561 58.1355H104.753L108.496 61.6315V74.245H104.327V71.9503L101.734 74.245H95.0807L91.8623 71.2726L91.8706 71.2649ZM101.434 70.9415L103.952 68.7083V67.638H97.3653L96.4231 68.5081V70.1252L97.2986 70.9338H101.434V70.9415Z"
                            fill="currentCollor" />
                        <path
                            d="M112.466 57.9934H116.69V60.7059L119.462 57.9934H124.379V61.6463H120.394L117.037 64.9106V74.245H112.466V57.9856V57.9934Z"
                            fill="currentCollor" />
                        <path
                            d="M128.305 70.9465V61.7219H125.204V58.1307H128.371V52.8904H132.85V58.1307H138.084V61.7528H132.85V69.5516L134.023 70.6228H138.092V74.2448H131.857L128.305 70.9465Z"
                            fill="currentCollor" />
                        <path
                            d="M141.239 57.9934H145.453V60.7059L148.325 57.9934H153.888L157.723 61.6152V74.245H153.164V62.7888L151.987 61.6463H149.082L145.799 64.7785V74.245H141.239V57.9856V57.9934Z"
                            fill="currentCollor" />
                        <path
                            d="M161.47 70.9354V61.499L165.078 58.1355H174.912L178.553 61.499V67.6103H166.095V69.6808L167.211 70.7506H172.92L173.97 69.7501V68.9111H178.52V71.0123L175.053 74.245H165.011L161.478 70.9508L161.47 70.9354ZM173.928 64.6316V62.7536L172.737 61.6222H167.278L166.086 62.7536V64.6316H173.928Z"
                            fill="currentCollor" />
                        <path
                            d="M182.224 57.9934H186.474V60.7059L189.264 57.9934H194.212V61.6463H190.202L186.823 64.9106V74.245H182.224V57.9856V57.9934Z"
                            fill="currentCollor" />
                        <rect x="13.7861" y="22.4697" width="51.0259" height="51.0259" fill="url(#paint0_linear_111_146)" />
                        <rect x="35.9326" y="29.6597" width="21.57" height="21.57" fill="white" />
                        <rect x="20.9766" y="51.2295" width="14.9552" height="14.9552" fill="white" />
                        <defs>
                            <linearGradient id="paint0_linear_111_146" x1="64.8121" y1="22.4697" x2="9.86106" y2="73.4957"
                                gradientUnits="userSpaceOnUse">
                                <stop stop-color="#5196E8" />
                                <stop offset="1" stop-color="#91FF6B" />
                            </linearGradient>
                        </defs>
                    </svg>
                </a>
            </div>

            {{ $slot }}
        </div>
    </body>
</html>
