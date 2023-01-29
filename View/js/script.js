//удобная обертка
$ = function (selsctor, obj = null) {
    if (obj === null) { obj = document; }
    return obj.querySelector(selsctor);
}
$$ = function (selsctor, obj = null) {
    if (obj === null) { obj = document; }
    return obj.querySelectorAll(selsctor);
}

$T = function (callback, delay = 1, $arg = null) {
    setTimeout(callback, delay, $arg);
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
    let slider = $(".gallery-slider");
    step = orientation == "left" ? Math.abs(step) : Math.abs(step) * (-1);
    let curPose = parseInt(window.getComputedStyle(slider).getPropertyValue('left'));
    let width = slider.clientWidth;
    let parentWidth = slider.parentNode.clientWidth;


    if ((step > 0 && curPose >= 0) || (curPose + step > 0)) {
        curPose = 0;
        step = 0;
    }
    if ((step < 0 && width + curPose <= parentWidth) || (width + curPose + step < parentWidth)) {
        curPose = (width - parentWidth) * (-1);
        step = 0;
    }

    slider.style.setProperty("left", (curPose + step) + "px")

}


function galleryShow(el) {
    galleryHide(true);

    let src = el.getAttribute("data-src");
    let text = el.getAttribute("title");

    let shadow = document.createElement('div');
    shadow.classList.add('gallery-popup-shadow');
    shadow.addEventListener("click", () => galleryHide());
    document.body.appendChild(shadow);
    $T(() => shadow.style.cssText = "opacity:1");

    let form = document.createElement('div');
    form.classList.add('gallery-popup-form');
    document.body.appendChild(form);
    $T(() => form.style.cssText = "opacity:1; transform: scale(1)");

    let img = document.createElement('img');
    img.classList.add('gallery-popup-img');
    img.src = src;
    img.alt = text;
    form.appendChild(img);


    let title = document.createElement('span');
    title.classList.add('gallery-popup-title');
    title.innerHTML = text;
    form.appendChild(title);


    let btnClose = document.createElement('span');
    btnClose.classList.add('gallery-popup-btn-close');
    btnClose.innerHTML = "X";
    btnClose.addEventListener("click", () => galleryHide());
    form.appendChild(btnClose);
}

function galleryHide(fast = false) {

    if (!!$(".gallery-popup-shadow")) { $(".gallery-popup-shadow").style.cssText = "" };
    if (!!$(".gallery-popup-form")) { $(".gallery-popup-form").style.cssText = "" };

    delay = 500;
    if (fast) {
        $$(".gallery-popup-shadow").forEach((el) => { el.remove(); });
        $$(".gallery-popup-form").forEach((el) => { el.remove(); });
        return;

    }

    $T(() => {
        $$(".gallery-popup-shadow").forEach((el) => { el.remove(); });
        $$(".gallery-popup-form").forEach((el) => { el.remove(); });
    }, delay);








}
function galleryloadImages(grpup) {
    fetch(window.baseURL + 'gallery/' + grpup).then(resp => {
        if (!resp.ok) { alert(resp.statusText); return; }
        return resp.json();
    }).then(json => {
        if (!json.hasOwnProperty("success")) { alert("Ошибка получения изображений"); return; }
        $$(".gallery-thumb").forEach(el => el.remove());
        $(".gallery-slider").style.cssText = "";
        $(".gallery").style.cssText = "";
        json.images.forEach((img, idx) => {

            let thumb = document.createElement('img');
            thumb.classList.add('gallery-thumb');
            thumb.src = window.baseURL + "img/gallery/" + img.gname + "/" + img.img;
            thumb.alt = img.description;
            thumb.setAttribute("data-src", window.baseURL + "img/gallery/" + img.gname + "/" + img.img);
            thumb.setAttribute("title", img.description);
            thumb.addEventListener("click", el => galleryShow(el.target));
            thumb.style.cssText = "transition: all 1s; opacity:0;";

            $(".gallery-slider").appendChild(thumb);
            $T((th) => { th.style.cssText = "transition: all 0.2s, opacity 1s ; "; }, 100 * (idx + 1), thumb);
        });
    });




}
