import "swiper/css/bundle";
import Swiper from "swiper/bundle";

function initializeSwiper() {
    new Swiper(".swiper", {
        // Optional parameters
        direction: "horizontal",
        freeMode: true,
        // Default parameters
        slidesPerView: 1,
        spaceBetween: 15,
        // Responsive breakpoints
        breakpoints: {
            // when window width is >= 550px
            550: {
                slidesPerView: 2,
                spaceBetween: 15,
            },
            // when window width is >= 768px
            768: {
                slidesPerView: 3,
                spaceBetween: 15,
            },
            // when window width is >= 1200px
            1200: {
                slidesPerView: 4,
                spaceBetween: 15,
            },
        },
    });
}

initializeSwiper();

document.addEventListener("livewire:navigated", () => {
    // Reinitialize Swiper after Livewire navigation
    initializeSwiper();
});
