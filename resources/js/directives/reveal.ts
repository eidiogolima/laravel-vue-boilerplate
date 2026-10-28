import type { Directive } from 'vue';

const revealObserver =
    typeof IntersectionObserver === 'undefined'
        ? null
        : new IntersectionObserver(
              (entries) => {
                  entries.forEach((entry) => {
                      if (!entry.isIntersecting) {
                          return;
                      }

                      entry.target.classList.add('is-visible');
                      revealObserver?.unobserve(entry.target);
                  });
              },
              { threshold: 0.15, rootMargin: '0px 0px -40px 0px' },
          );

export const vReveal: Directive<HTMLElement, number | undefined> = {
    mounted(element, binding) {
        element.classList.add('landing-reveal');
        element.style.setProperty('--landing-delay', `${binding.value ?? 0}ms`);

        if (revealObserver === null) {
            element.classList.add('is-visible');

            return;
        }

        revealObserver.observe(element);
    },
    unmounted(element) {
        revealObserver?.unobserve(element);
    },
};
