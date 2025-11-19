<x-hgrh::layouts.app pageName="App home">
    <div class="modal fade" id="versionModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">An update is available</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Please click OK to update your app to version {{setting('app_version')}}
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="refresh();" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    <button id="installbutton" class="btn btn-success" hidden>
        <i class="bi bi-download"></i> Install App
    </button>
    <img width="100%" src="{{ asset('hgrh/images/header.png') }}" />
    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="pills-browse-tab" data-bs-toggle="pill" data-bs-target="#pills-browse" type="button" role="tab" aria-controls="pills-browse" aria-selected="true">Browse</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="pills-search-tab" data-bs-toggle="pill" data-bs-target="#pills-search" type="button" role="tab" aria-controls="pills-search" aria-selected="false">Search</button>
        </li>
    </ul>
    <div class="tab-content" id="pills-tabContent">
        <div class="tab-pane fade show active" id="pills-browse" role="tabpanel" aria-labelledby="pills-browse-tab">
            @livewire('file-browser')
        </div>
        <div class="tab-pane fade" id="pills-search" role="tabpanel" aria-labelledby="pills-search-tab">
            @livewire('search')
        </div>
    </div>
    <script>
        function refresh() {
            console.log('refreshing');
            setCookie("hgrh-version", "{{setting('app_version')}}", 365);
            window.location.reload();
        }

        function setCookie(cname, cvalue, exdays) {
            const d = new Date();
            d.setTime(d.getTime() + (exdays*24*60*60*1000));
            let expires = "expires="+ d.toUTCString();
            document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
        }

        function getCookie(cname) {
            let name = cname + "=";
            let decodedCookie = decodeURIComponent(document.cookie);
            let ca = decodedCookie.split(';');
            for(let i = 0; i <ca.length; i++) {
                let c = ca[i];
                while (c.charAt(0) == ' ') {
                c = c.substring(1);
                }
                if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
                }
            }
            return "";
        }

        function disableInAppInstallPrompt() {
            installPrompt = null;
            const installButton = document.querySelector("#installbutton");
            if (installButton) {
                installButton.setAttribute("hidden", "");
            }
        }

        window.addEventListener('load', function() {
            let version = getCookie("hgrh-version");
            newversion = "{{setting('app_version')}}";
            if (version !== newversion){
                var modal = new bootstrap.Modal(document.getElementById('versionModal'))
                modal.show();
            }
            console.log('Version: ' + version);
            
            let installPrompt = null;
            const installButton = document.querySelector("#installbutton");
            
            window.addEventListener("beforeinstallprompt", (event) => {
                event.preventDefault();
                installPrompt = event;
                if (installButton) {
                    installButton.removeAttribute("hidden");
                }
            });

            if (installButton) {
                installButton.addEventListener("click", async () => {
                    if (!installPrompt) {
                        return;
                    }
                    const result = await installPrompt.prompt();
                    console.log(`Install prompt was: ${result.outcome}`);
                    disableInAppInstallPrompt();
                });
            }

            window.addEventListener("appinstalled", () => {
                disableInAppInstallPrompt();
            });
        })
    </script>
</x-hgrh::layouts.app>