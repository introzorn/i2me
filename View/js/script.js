//удобная обертка
$ = function (selsctor, obj = null) {
    if (obj === null) { obj = document; }
    return obj.querySelector(selsctor);
}
$$ = function (selsctor, obj = null) {
    if (obj === null) { obj = document; }
    return obj.querySelectorAll(selsctor);
}


window.addEventListener("DOMContentLoaded", function () {

    gellaryInit();

});



function gellaryInit() {

    $$(".gallery-btn").forEach(element => {

        element.addEventListener("click", function (event) {
            if (event.target.classList.contains('gallery-btn-left')) {
                galleryList("left");
            } else {
                galleryList("right");
            }
        });


    });

}

function galleryList(orientation, step = 250) {
    slider = $(".gallery-slider");
    step = orientation == "left" ? Math.abs(step) : Math.abs(step) * (-1);
    curPose = parseInt(window.getComputedStyle(slider).getPropertyValue('left'));
    width = slider.clientWidth;
    parentWidth = slider.parentNode.clientWidth;


    if ((step > 0 && curPose >= 0) || (curPose + step > 0)) {
        curPose = 0;
        step = 0;
    }
    if ((step < 0 && width + curPose <= parentWidth) || (width + curPose + step < parentWidth)) {
        curPose = (width - parentWidth) * (-1);
        step = 0;
    }

    console.log(width + curPose, parentWidth, curPose);

    slider.style.setProperty("left", (curPose + step) + "px")
    //   console.log(parseInt(curPose));
}