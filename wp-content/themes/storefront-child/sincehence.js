// let scrollTimer;
// window.addEventListener("scroll", () => {
//     document.body.classList.add("scrolling");

//     // Clear any existing timer
//     clearTimeout(scrollTimer);

//     // Remove the class after scrolling stops (for example, after 150ms)
//     scrollTimer = setTimeout(() => {
//         document.body.classList.remove("scrolling");
//     }, 150);
// });

let isScrolling;

window.addEventListener('scroll', () => {
    const content = document.querySelector('#content'); // Select the #content element
    content.classList.add('scrolling');

    clearTimeout(isScrolling);

    isScrolling = setTimeout(() => {
        content.classList.remove('scrolling');
    }, 150);
});
