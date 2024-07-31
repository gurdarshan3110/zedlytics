<!-- <footer class="py-3  mt-auto">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted"></div>
                            <div>
                                <a href="#">Privacy Policy</a>
                                &middot;
                                <a href="#">Terms &amp; Conditions</a>
                            </div>
                        </div>
                    </div>
                </footer> -->
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="{{asset('/assets/js/scripts.js')}}"></script>
        <script type="text/javascript">
            $( document ).ready(function() {
                $('input').attr('autocomplete','off');
                var menuVal = localStorage.getItem('menu');
                if(menuVal==1){
                    document.body.classList.remove('sb-sidenav-toggled');
                }else{
                    document.body.classList.add('sb-sidenav-toggled');
                }
                $('#sidebarToggle').click(function(){
                    if(menuVal==true){
                        localStorage.setItem('menu',0);
                        document.body.classList.add('sb-sidenav-toggled');
                    }else{
                        localStorage.setItem('menu',1);
                        document.body.classList.remove('sb-sidenav-toggled');
                    }
                })
            });
        </script>
        @stack('jsscript')
    </body>
</html>