document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.querySelector('.nav-toggle');
    const navigation = document.querySelector('.nav-links');
    if (!toggle || !navigation) return;

    function setOpen(open, focusNavigation) {
        toggle.setAttribute('aria-expanded', String(open));
        toggle.setAttribute('aria-label', open ? 'Close navigation' : 'Open navigation');
        navigation.classList.toggle('is-open', open);

        if (open && focusNavigation) {
            navigation.querySelector('a')?.focus();
        }
    }

    toggle.addEventListener('click', function () {
        const open = toggle.getAttribute('aria-expanded') !== 'true';
        setOpen(open, open);
    });

    navigation.addEventListener('click', function (event) {
        if (event.target.closest('a')) setOpen(false, false);
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') {
            setOpen(false, false);
            toggle.focus();
        }
    });

    window.addEventListener('resize', function () {
        if (window.innerWidth > 768) setOpen(false, false);
    });
});