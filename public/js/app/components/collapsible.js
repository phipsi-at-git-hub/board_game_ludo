document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.collapsible-item').forEach(item => {
        const header = item.querySelector('.collapsible-header');
        const content = item.querySelector('.collapsible-content');
        if (!header || !content) {
            return;
        }

        item.addEventListener('click', () => {
            if (event.target.closest('.history-state')) {
                return;
            }

            const isOpen = item.classList.contains('is-open');
            if (isOpen) {
                closeCollapsible(item);
            } else {
                openCollapsible(item);
            }
        });
    });

    function openCollapsible(item) {
        const header = item.querySelector('.collapsible-header');
        const content = item.querySelector('.collapsible-content');

        item.classList.add('is-open');
        header.setAttribute('aria-expanded', 'true');

        /*
         * Start at zero so the browser has a definite
         * starting point for the height transition.
         */
        content.style.height = '0px';

        /*
         * Force layout before changing the height.
         */
        content.offsetHeight;
        const targetHeight = content.scrollHeight;
        content.style.height = `${targetHeight}px`;
        content.addEventListener(
            'transitionend',
            () => {

                /*
                 * Once the animation is finished, use auto
                 * so dynamic content can still behave normally.
                 */
                if (item.classList.contains('is-open')) {
                    content.style.height = 'auto';
                }

            },
            { once: true }
        );
    }

    function closeCollapsible(item) {
        const header = item.querySelector('.collapsible-header');
        const content = item.querySelector('.collapsible-content');

        /*
         * If the content currently uses "auto", we first
         * convert it to its actual pixel height.
         */
        const currentHeight = content.scrollHeight;
        content.style.height = `${currentHeight}px`;

        /*
         * Force layout before starting the transition.
         */
        content.offsetHeight;
        content.style.height = '0px';
        item.classList.remove('is-open');
        header.setAttribute('aria-expanded', 'false');
        content.addEventListener(
            'transitionend',
            () => {
                /*
                 * Only reset the inline height after the
                 * closing animation has completed.
                 */
                if (!item.classList.contains('is-open')) {
                    content.style.height = '0px';
                }
            },
            { once: true }
        );
    }
});
