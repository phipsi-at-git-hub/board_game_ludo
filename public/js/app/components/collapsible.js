document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.collapsible-item').forEach(item => {
        const header = item.querySelector('.collapsible-header');
        const content = item.querySelector('.collapsible-content');
        if (!header || !content) {
            return;
        }

        item.addEventListener('click', event => {
            if (event.target.closest('.collapsible-no-toggle')) {
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

    function getContentHeight(content) {
        const inner = content.querySelector('.collapsible-content-inner'); 
        if (inner) {
            return inner.offsetHeight; 
        }
        return content.scrollHeight; 
    }

    function openCollapsible(item) {
        const header = item.querySelector('.collapsible-header');
        const content = item.querySelector('.collapsible-content');

        item.classList.add('is-open');
        header.setAttribute('aria-expanded', 'true');

        item.style.overflow = 'hidden'; 

        /**
         * Keep overflow hidden during the opening animation.
         */
        content.style.overflow = 'hidden'; 

        /**
         * Start at zero so the browser has a definite
         * starting point for the height transition.
         */
        content.style.height = '0px';

        /**
         * Force layout before changing the height.
         */
        content.offsetHeight;
        //const targetHeight = content.scrollHeight;
        const targetHeight = getContentHeight(content); 
        content.style.height = `${targetHeight}px`;
        content.addEventListener(
            'transitionend',
            () => {

                /**
                 * Once the animation is finished, use auto
                 * so dynamic content can still behave normally.
                 */
                if (item.classList.contains('is-open')) {
                    content.style.height = 'auto'; 
                    content.style.overflow = 'visible'; 
                    item.style.overflow = 'visible'; 
                }

            },
            { once: true }
        );
    }

    function closeCollapsible(item) {
        const header = item.querySelector('.collapsible-header');
        const content = item.querySelector('.collapsible-content');

        item.style.overflow = 'hidden'; 
        content.style.overflow = 'hidden'; 

        /**
         * If the content currently uses "auto", convert
         * its actual content height into a pixel value
         */
        //const currentHeight = content.scrollHeight;
        const currentHeight = getContentHeight(content); 
        content.style.height = `${currentHeight}px`;

        /**
         * Force layout before starting the transition.
         */
        content.offsetHeight;
        content.style.height = '0px';
        item.classList.remove('is-open');
        header.setAttribute('aria-expanded', 'false');
        content.addEventListener(
            'transitionend',
            () => {
                /**
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
