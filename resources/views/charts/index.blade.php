@extends('includes.app')

@section('content')

<main>
    <div class="container-fluid px-4">
        <div class="d-flex">
            <h3 class="mt-4 w-95">
                {{$title}}
            </h3>
            
        </div>
        <div class="d-flex flex-shrink-0">
            
            <!--end::Create app-->
        </div>
        <div class="card mb-4" style="height:520px;">
            <div class="card-body">
                    <div class="h-100">                        <div class="tradingview-widget-container" style="height:100%;width:100%">
                          <div class="tradingview-widget-container__widget"></div>
                          
                          <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-advanced-chart.js" async>
                          {
                          "autosize": true,
                          "symbol": "NASDAQ:AAPL",
                          "timezone": "Etc/UTC",
                          "theme": "light",
                          "style": "1",
                          "locale": "en",
                          "withdateranges": true,
                          "range": "YTD",
                          "hide_side_toolbar": false,
                          "allow_symbol_change": true,
                          "details": true,
                          "hotlist": true,
                          "calendar": false,
                          "show_popup_button": true,
                          "popup_width": "100%",
                          "popup_height": "100%",
                          "support_host": "https://www.tradingview.com"
                        }
                          </script>
                        </div>
                    </div>

            </div>
        </div>
    </div>
</main>
 @endsection  
 