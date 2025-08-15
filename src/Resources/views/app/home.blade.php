<x-hgrh::layouts.app pageName="App home">
    <div class="modal fade" id="versionModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">An update is available</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Please click OK to update your app to version xx
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="refresh();" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function refresh() {
            console.log('refreshing');
            setCookie("wmc-version", "{{setting('general.app_version')}}", 365);
            window.location.reload();
        }

        function setCookie(cname, cvalue, exdays) {
            const d = new Date();
            d.setTime(d.getTime() + (exdays*24*60*60*1000));
            let expires = "expires="+ d.toUTCString();
            document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
        }

        window.addEventListener('load', function() {
            let version = getCookie("wmc-version");
            newversion = "{{setting('general.app_version')}}";
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
                installButton.removeAttribute("hidden");
            });

            installButton.addEventListener("click", async () => {
                if (!installPrompt) {
                    return;
                }
                const result = await installPrompt.prompt();
                console.log(`Install prompt was: ${result.outcome}`);
                disableInAppInstallPrompt();
            });

            window.addEventListener("appinstalled", () => {
                disableInAppInstallPrompt();
            });

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
                installButton.setAttribute("hidden", "");
            }
        })
    </script>
</x-hgrh::layouts.app>