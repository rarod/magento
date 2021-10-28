define(["jquery", "slick"], function ($) {
    $(".home-appeals .pagebuilder-column-group").slick({
        autoplay: false,
        infinite: false,
        slidesToShow: 3,
        arrows: false,
        responsive: [
            {
                breakpoint: 801,
                settings: {
                    slidesToShow: 2,
                    infinite: true,
                },
            },
            {
                breakpoint: 550,
                settings: {
                    slidesToShow: 1,
                    infinite: true,
                    dots: true,
                },
            },
        ],
    });

    $(
        ".product-list.product-with-banner .block-products-list.grid .widget-product-grid"
    ).slick({
        autoplay: false,
        infinite: true,
        slidesToShow: 3,
        arrows: true,
        responsive: [
            {
                breakpoint: 801,
                settings: {
                    slidesToShow: 3,
                    infinite: true,
                },
            },
            {
                breakpoint: 550,
                settings: {
                    slidesToShow: 2,
                    infinite: true,
                    dots: true,
                },
            },
        ],
    });

    $(".block-static-block .categories-carrossel").slick({
        autoplay: false,
        infinite: true,
        slidesToShow: 5,
        arrows: true,
        responsive: [
            {
                breakpoint: 801,
                settings: {
                    slidesToShow: 3,
                    infinite: true,
                },
            },
            {
                breakpoint: 550,
                settings: {
                    slidesToShow: 2,
                    infinite: true,
                    dots: true,
                },
            },
        ],
    });

    $(".banners-mosaic .pagebuilder-column-group").slick({
        autoplay: false,
        infinite: true,
        slidesToShow: 3,
        arrows: false,
        responsive: [
            {
                breakpoint: 801,
                settings: {
                    slidesToShow: 2,
                    infinite: true,
                    dots: true,
                },
            },
            {
                breakpoint: 550,
                settings: {
                    slidesToShow: 1,
                    infinite: true,
                    dots: true,
                },
            },
        ],
    });
});
