//удобная обертка
function ajax(surl, smethod, sdata, callback) {
    axios({
        method: smethod,
        url: surl,
        data: sdata,
        headers: { 'content-type': 'application/x-www-form-urlencoded', 'a-type': 'ajax' },
        baseURL: window.baseURL
    }).then(function (response) { callback(response); });
}


// //переключаем обработку
// function obrabotka(bool){
//     m='';
//     if(bool){
//         document.getElementById("b_array").className="btn-sm btn-dark";
//         document.getElementById("b_sql").className="btn-sm btn-secondary";
//         m='1';
//     }else{
//         document.getElementById("b_array").className="btn-sm btn-secondary";
//         document.getElementById("b_sql").className="btn-sm btn-dark";

//     }
//     ajax("/adminprocess", "post", "cmd=obrabotka&val="+m, function (resp) {

        
//         reload_items();
//     });

// }


//переключаем сортировку корневых элементов

function sorted(){
    sort=document.getElementById("sorted").value;
    ajax("/adminprocess", "post", "cmd=sorted&val="+sort, function (resp) {
        reload_items();
    });
}



//подгрузка страницы pagin()
function pagin(event, page) {
    if (event) {
        event.preventDefault();
        event.stopImmediatePropagation();
    }
    paginator = document.querySelector('.paginator');
    tree_cont = document.querySelector('.tree_cont');
    paginator.style.setProperty('opacity', '0.3');
    tree_cont.style.setProperty('opacity', '0.3');
    surl = window.baseURL;
    if (window.category != '') { surl += window.category + '/'; }
    surl += 'p' + page;

    ajax(surl, 'get', '', function (resp) {
        if (resp.data['content']) {
            paginator = document.querySelector('.paginator');
            tree_cont = document.querySelector('.tree_cont');
            paginator.innerHTML = resp.data['PAGINATOR'];
            if (!resp.data['rootbtn']) { resp.data['rootbtn'] = ''; }
            tree_cont.innerHTML = resp.data['rootbtn'] + resp.data['content'];
            window.category = resp.data['category'];
            window.page = resp.data['page'];


            surl = window.baseURL;
            if (window.category != '') { surl += window.category + '/'; }
            surl += 'p' + page;

            history.pushState({ nav: 'bar' }, document.title, surl);
        }
        paginator.style.setProperty('opacity', '1');
        tree_cont.style.setProperty('opacity', '1');

    });
    return false
}
function pagin2(href) {

    paginator = document.querySelector('.paginator');
    tree_cont = document.querySelector('.tree_cont');
    paginator.style.setProperty('opacity', '0.3');
    tree_cont.style.setProperty('opacity', '0.3');
    surl = window.baseURL;

    surl = href;
    ajax(surl, 'get', '', function (resp) {
        if (resp.data['content']) {
            paginator = document.querySelector('.paginator');
            tree_cont = document.querySelector('.tree_cont');
            paginator.innerHTML = resp.data['PAGINATOR'];
            if (!resp.data['rootbtn']) { resp.data['rootbtn'] = ''; }
            tree_cont.innerHTML = resp.data['rootbtn'] + resp.data['content'];
            window.category = resp.data['category'];
            window.page = resp.data['page'];


            surl = window.baseURL;
            if (window.category != '') { surl += window.category + '/'; }
            surl += 'p' + page;


        }
        paginator.style.setProperty('opacity', '1');
        tree_cont.style.setProperty('opacity', '1');

    });
    return false;
}



function reload_items() {
    pagin(null, window.page);
}
window.addEventListener('popstate', function (e) {
    href = location.href.split("/");
    pg = href[href.length - 1];
    cat = href[href.length - 2];
    if (cat != window.category) { location.reload(); return; }

    pagin2(location.href);

    // if(href[count[href]-1])
});


//показать форму авторизации
function showLogin() {
    document.getElementById("podlogka").style.display = 'block';
    document.getElementById("loginblock").style.display = 'block';
}
function ajaxAuth(event, elem) {
    var Form = new FormData(elem);
    Form.append("type", "ajax");
    type = elem.getAttribute("ma");
    document.getElementById("btn-" + type).style.display = 'none';
    document.getElementById("load-" + type).style.display = 'inline-block';
    ajax("/process", "post", Form, function (resp) {

        if (resp.data['error']) {
            document.getElementById(resp.data['type'] + "error").innerHTML = resp.data['error'];
            document.getElementById("btn-" + resp.data['type']).style.display = 'inline-block';
            document.getElementById("load-" + resp.data['type']).style.display = 'none';
            return;
        }
        console.log(resp.data);
        if (resp.data['redirect']) { location.href = resp.data['redirect']; }
    });
    event.preventDefault();
    event.stopImmediatePropagation();
}


//показать форму регистрации
function showReg() {
    document.getElementById("podlogka").style.display = 'block';
    document.getElementById("regblock").style.display = 'block';
}
//сокрытие форм авторизации
function hideForms() {
    document.getElementById("podlogka").style.display = 'none';
    document.getElementById("loginblock").style.display = 'none';
    document.getElementById("regblock").style.display = 'none';
}



//развертывание элементов
function slide(btn) {
    id = btn.parentNode.parentNode.getAttribute('vkl');
    if (btn.getAttribute('rel') != 'active') {
        btn.setAttribute('rel', 'active');
        document.getElementById('slide_' + id).style.display = "block";
    } else {
        btn.setAttribute('rel', '');
        document.getElementById('slide_' + id).style.display = "none";
    }



}



//скрыть формы
function hideMSGBOX() {
    document.getElementById("msgbox").style.display = 'none';

}
//показать форму добавления
function showAdd(id) {

    if (document.getElementById("podlogka2").style.getPropertyValue("display") == 'block') { return false; }

   
    document.getElementById("msgbox_loader").style.display = 'none';
    document.getElementById("delltree").style.display = 'none';
    document.getElementById("addtree").style.display = 'block';
    document.getElementById("msgbox").style.display = 'block';
    document.getElementById("prnt_name").value = id;

    document.getElementById("addedit").value = 'add';
    document.getElementById("vname").value = '';
    // document.getElementById("vcont").setAttribute('value','');
    document.getElementById("vcont").value = '';
    document.getElementById("addedit_text").innerHTML = "Добавить";
    document.getElementById("addedit_btn").innerHTML = "Добавить";
    document.getElementById("id_edit").value = id;

    drawParent(id);
  
   
    document.getElementById("itempose").value=document.getElementById("itempose").getAttribute("max")
}

//показать форму удаления
function showDel(id, name) {
    document.getElementById("delltree").style.display = 'block';
    document.getElementById("addtree").style.display = 'none';
    document.getElementById("msgbox_loader").style.display = 'none';
    document.getElementById("msgbox").style.display = 'block';
    document.getElementById("id_name").innerHTML = name;
    document.getElementById("id_dell").value = id;

}


//процедура удаления вкладики
function goDell(event) {
    event.preventDefault();
    event.stopImmediatePropagation();

    document.getElementById("msgbox").style.display = 'block';
    document.getElementById("delltree").style.display = 'none';
    document.getElementById("addtree").style.display = 'none';
    document.getElementById("msgbox_loader").style.display = 'flex';

    var Form = new FormData(document.getElementById("delltree"));
    Form.append('type', 'ajax');

    ajax("/adminprocess", "post", Form, function (resp) {

        if (resp.data['error']) { //на случай ошибки
            alert(resp.data['error']);
            hideMSGBOX();
            return;
        }

        id = resp.data['id'];

        $name = document.getElementById("vkl_name_" + id).innerHTML;
        elem = document.getElementById("vkl_" + id);
        mel = elem;
        do {
            el = mel.previousSibling;
            console.log(el);
            if (el == null) { break; }
            if (el.nodeName != "#text") {
                if (el.className != "vkl_interval") { break; }
                el.parentNode.removeChild(el);

            } else { mel = el; }

        } while (true);

        el = elem.nextSibling;
        if (el || el.nodeName == "BR") { el.parentNode.removeChild(el); }


        parent = elem.getAttribute('parent');
        elem.parentNode.removeChild(elem);
        sli = document.getElementById("slide_" + id);
        if (sli != null) {
            sli.parentNode.removeChild(sli);
        }


        hideMSGBOX();
    });
    return false;

}

//показать форму редактирования
function edit(id) {
    document.getElementById("msgbox").style.display = 'block';
    document.getElementById("delltree").style.display = 'none';
    document.getElementById("addtree").style.display = 'none';
    document.getElementById("msgbox_loader").style.display = 'flex';

    document.getElementById("itempose").value=document.getElementById("vkl_"+id).getAttribute("itempose");

    var date = new Date;
    
    if(window.cachedata[id] && !window.cachedata[id]['error'] && window.cachedata[id]['cacheTime']>date.getTime() ){
        edit_a(window.cachedata[id]); 
        return;
    }

    ajax("/adminprocess", "post", "cmd=getitem&type=ajax&id=" + id, function (resp) {
        
        edit_a(resp.data);
        if(!resp.data["error"]){
            window.cachedata[resp.data["id"]]=resp.data;
            var date = new Date();
            date.setMinutes(date.getMinutes() + 5);
            window.cachedata[resp.data["id"]]['cacheTime']=date.getTime();
        }
    });
}


function edit_a(data) {

    if (data['error']) { //на случай ошибки
        alert(data['error']);
        hideMSGBOX();
        return;
    }



    id = data['id'];

    document.getElementById("msgbox_loader").style.display = 'none';
    document.getElementById("delltree").style.display = 'none';
    document.getElementById("addtree").style.display = 'block';
    document.getElementById("msgbox").style.display = 'block';
    // p = document.getElementById("vkl_" + id).getAttribute('parent');

    document.getElementById("prnt_name").value = data['parent'];
    document.getElementById("addedit").value = 'edit';
    // n = document.getElementById("vkl_name_" + id).innerHTML;

    // c = document.getElementById("vkl_cont_" + id).textContent ;
    document.getElementById("vname").value = data['name'];
    // document.getElementById("vcont").setAttribute('value',c);
    if (document.getElementById("vkl_cont_" + id).getAttribute('nullcontent') == 1) { c = ''; }

    document.getElementById("vcont").value = data['content'];
    document.getElementById("id_edit").value = id;
    document.getElementById("addedit_text").innerHTML = "Изменить";
    document.getElementById("addedit_btn").innerHTML = "Изменить";
    drawParent(data['parent']);
   
    
}


//редрав родителя в окне редактирования
function drawParent(parent) {
    if (parent != 0) {
        cls = document.getElementById("vkl_" + parent).className;
        n = document.getElementById("vkl_name_" + parent).innerHTML;
    } else {
        cls = "btn-outline-info";
        n = "Корневой эелемент";
    }
    document.getElementById("view_parent").innerHTML = parent + ' -> ' + n;
    document.getElementById("view_parent").className = cls + ' btn sbnp';

    el=document.getElementById("slide_"+parent);
    if(el){
      max=Number(el.getAttribute("max"))+1;  
    }else{max=1;}
    
    document.getElementById("itempose").setAttribute("max",max);
  
}

//включение режима выбора родителя
function selectParent() {

    document.getElementById("podlogka2").style.setProperty("display", "block");
    document.getElementById("scroller").style.setProperty("z-index", "999999");
    el = document.querySelectorAll(".ccontrol");
    for (element of el) {
        element.style.setProperty("display", "none");
    }
    btn = document.querySelectorAll(".btnsel");
    for (element of btn) {
        element.style.setProperty("display", "inline-block");
    }



    id = document.getElementById("id_edit").value;

    if (id == '') { id = 0; }
    document.getElementById("sel_" + id).style.setProperty("display", "none");

}

//назначение родителя
function doSelect(id) {

    document.getElementById("podlogka2").style.setProperty("display", "none");
    document.getElementById("scroller").style.setProperty("z-index", "auto");
    el = document.querySelectorAll(".ccontrol");
    for (element of el) {
        element.style.setProperty("display", "inline");
    }
    btn = document.querySelectorAll(".btnsel");
    for (element of btn) {
        element.style.setProperty("display", "none");
    }

    document.getElementById("prnt_name").value = id;
    drawParent(id);
}

//отключение режима выбора родителя
function hideSelect() {
    document.getElementById("podlogka2").style.setProperty("display", "none");
    document.getElementById("scroller").style.setProperty("z-index", "auto");
    el = document.querySelectorAll(".ccontrol");
    for (element of el) {
        element.style.setProperty("display", "inline");
    }
    btn = document.querySelectorAll(".btnsel");
    for (element of btn) {
        element.style.setProperty("display", "none");
    }

}


//секция прямого вызова при загрузки страницы
window.cachedata=[];



window.onresize = function () {
    h1 = document.getElementById("topcontainer").clientHeight;
    h2 = document.getElementById("footer").clientHeight;

    document.getElementById("scroller").style.setProperty("height", 'calc (100vh-' + (h2 + h1) + "px)");
};

document.addEventListener("DOMContentLoaded", function (event) {
    window.addEventListener('resize', function () {
        scrollersize();
    });
    scrollersize();
});
//секция прямого вызова при загрузки страницы




function scrollersize() {

    h1 = document.getElementById("topcontainer").offsetHeight;
    h2 = document.getElementById("botcontainer").offsetHeight;
    h3 = document.getElementById("paginator").offsetHeight;
    h = window.innerHeight - h1 - h2 - h3 - 10;

    document.getElementById("scroller").style.height = h + 'px';

}

//показываем контент вкладки или скрываем его
function showContent(elem) {

    id = elem.parentNode.getAttribute('vkl');
    display = document.getElementById("vkl_cont_" + id).style.getPropertyValue('display');
    if (display == 'block') {
        document.getElementById("vkl_cont_" + id).style.setProperty('display', 'none');
        elem.setAttribute('title', 'нажмите чтобы посмотреть описание');
    } else {
        document.getElementById("vkl_cont_" + id).style.setProperty('display', 'block');
        elem.setAttribute('title', 'нажмите чтобы скрыть описание');
    }
    loadContent(id);
}


//ajax подгрузка контента
function loadContent(eid) {
    if (document.getElementById('vkl_cont_' + eid).getAttribute("nullcontent") == 1) { return; }
    if (document.getElementById('vkl_cont_' + eid).getAttribute("nullcontent") == 2) { return; }
    document.getElementById('vkl_cont_' + eid).innerHTML = '<center><img src="' + window.baseURL + 'img/load1.gif"></center>';
    ajax("/ajax_getcontent", "post", "id=" + eid, function (resp) {

        document.getElementById('vkl_cont_' + resp.data.id).innerHTML = resp.data.content;
        document.getElementById('vkl_cont_' + resp.data.id).setAttribute("nullcontent", 2);

    })

}

//валидация при добавлении или изменении
function valid(event) {
    event.preventDefault();
    event.stopImmediatePropagation();

    n = document.getElementById("vname").value.trim();

    if (n == '') {
        alert('Вы не ввели имя вкладки');
        return false;
    }
    if (n.length > 255) {
        alert('Описание не должно быть больше 255 символов');
        return false;
    }


    document.getElementById("msgbox").style.display = 'block';
    document.getElementById("delltree").style.display = 'none';
    document.getElementById("addtree").style.display = 'none';
    document.getElementById("msgbox_loader").style.display = 'flex';



    var Form = new FormData(document.getElementById("addtree"));
    Form.append('type', 'ajax');

    if (document.getElementById("addedit").value == 'edit') {
        id = document.getElementById("id_edit").value;
        cls = document.getElementById("vkl_" + id).className;
        document.getElementById("vkl_" + id).setAttribute('oldclass', cls);
        document.getElementById("vkl_" + id).className = 'vkl btn-white';
    }
    ajax("/adminprocess", "post", Form, function (resp) {
        if (resp.data['error']) {
            alert(resp.data['error']);
            hideMSGBOX();
            return;
        }

        item = resp.data;

        window.cachedata[resp.data["id"]]=resp.data;
        var date = new Date();
        date.setMinutes(date.getMinutes() + 5);
        window.cachedata[resp.data["id"]]['cacheTime']=date.getTime();

        //console.log(item);
        // cls=document.getElementById("vkl_"+item['id']).getAttribute('oldclass');

        // document.getElementById("vkl_"+item['id']).className=cls;

        //тут надо перезагрузить страничку
        reload_items();
        hideMSGBOX();


    });


    return false;

}
