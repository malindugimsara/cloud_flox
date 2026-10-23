<!--build:js-->
<script src="assets/js/vendors/jquery-3.7.1.min.js"></script>
<script src="assets/js/vendors/jquery-ui.min.js"></script>
<script src="assets/js/vendors/bootstrap.bundle.min.js"></script>
<script src="assets/js/vendors/swiper-bundle.min.js"></script>
<script src="assets/js/vendors/simplebar.min.js"></script>
<script src="assets/js/vendors/sal.js"></script>
<script src="assets/js/vendors/jquery.magnific-popup.min.js"></script>

<script src="assets/js/app.js"></script>
<script src="assets/js/cloudflox-domain-checker.js?v=4"></script>

<script>
    const OFFER_KEY = "cloudflox_offer_hidden";
    const HIDE_DURATION = 60 * 60 * 1000;

    function openOfferPopup() {
        const el = document.getElementById("offerPopup");
        if (!el) return;
        el.classList.remove("d-none");
    }

    function closeOfferPopup() {
        const dont = document.getElementById("dontShowOffer");

        if (dont && dont.checked) {
            // Save expiry time (current time + 1 hour)
            const expireTime = Date.now() + HIDE_DURATION;
            localStorage.setItem(OFFER_KEY, expireTime);
        }

        document.getElementById("offerPopup").classList.add("d-none");
    }

    // Auto show once
    window.addEventListener("load", () => {
        const savedExpire = localStorage.getItem(OFFER_KEY);

        // If expiry exists and still valid → do not show popup
        if (savedExpire && Date.now() < savedExpire) return;

        // Expired → remove and show again
        localStorage.removeItem(OFFER_KEY);

        setTimeout(openOfferPopup, 3000); // show after 3 sec
    });
</script>