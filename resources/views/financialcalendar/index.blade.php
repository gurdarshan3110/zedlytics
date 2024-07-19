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
                    <div class="h-100">                   
                        <div class="tradingview-widget-container">
                          <div class="tradingview-widget-container__widget"></div>
                          <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-events.js" async>
                          {
                          "colorTheme": "light",
                          "isTransparent": false,
                          "width": "100%",
                          "height": "100%",
                          "locale": "en",
                          "importanceFilter": "-1,0,1",
                          "countryFilter": "ar,au,br,ca,cn,fr,de,in,id,it,jp,kr,mx,ru,sa,za,tr,gb,us,eu"
                        }
                          </script>
                        </div>
                    </div>

            </div>
        </div>
    </div>
</main>
 @endsection  
 