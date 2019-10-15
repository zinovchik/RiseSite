jQuery(document).ready(function () {

    //выделение выбраного типа файлов
    jQuery("#panel > a:not('#choice_none, #choice_rise')").click(function () {
        jQuery("input." + this.id).prop("checked", true);
    });

    //снимаем все выделение
    jQuery("#choice_none").click(function () {
        jQuery("input.checkbox").prop("checked", false);
    });

    //снимаем выделение с уже востановленых файлов
    jQuery("#choice_rise").click(function () {
        jQuery("input.checkbox.choice_rise").prop("checked", false);
    });

    //запуск постановления
    jQuery("#choice_start").click(function () {
        jQuery("#terminal").css("display", "block");

        var counter = 1;
        var all = jQuery('input.checkbox:checked').length;
        var progress = 0;
        var test = '';
//        jQuery('input.checkbox:checked').each(function (i, elem) {
//            //test = test++;
//            tmp1 = this.value;
//            tmp1 = tmp1.replace(/&/g, 'ampersandtoreplase');
//            //tmp1 = encodeURIComponent(tmp1);
//            console.log("ajax.php" + "?url=" + tmp1 + "&limit=500&i=" + i);
//            jQuery("#progress_status").load("ajax.php" + "?url=" + tmp1 + "&limit=500&i=" + i, function (response, status, xhr) {
//                jQuery("#progress_list").append(response);
//                jQuery("#progress_status").text("");
//
//                progress = Math.round(counter / all * 100);
//                counter = counter + 1;
//                jQuery("#terminal #progress_line div").css('width', progress + '%');
//                jQuery("#terminal #progress_line span").text(progress + " %");
//
////if (counter > 1000){alert(counter);return;}
//
//            })
//        });
        //alert(counter + ' are sending');

        // function for circle ajax query
        function myEach(listPosts, i, maxLen){
            // get first element
            var one = listPosts.shift();
            one = one.replace(/&/g, 'ampersandtoreplase');
            console.log(one);
            // send ajax
            jQuery("#progress_status").load("ajax.php" + "?url=" + one + "&limit=500&i=" + i, function (response, status, xhr) {
                jQuery("#progress_list").append(response);
                jQuery("#progress_status").text("");

                progress = Math.round(counter / all * 100);
                counter = counter + 1;
                jQuery("#terminal #progress_line div").css('width', progress + '%');
                jQuery("#terminal #progress_line span").text(progress + " %");

                if(listPosts.length > 0){
                    i++;
                    myEach(listPosts, i, maxLen);
                } else {
                    alert("Rise Complete!");
                }
            });


            //jQuery.post(url, {i: i, link: one}).done(function(response) {
            //
            //    console.log(response);
            //    // update progress
            //    jQuery('.console .progress span').text( Math.round((i / maxLen) * 100) );
            //    jQuery('.console .info').prepend(i+') <a class="status_'+(response.success)+'" href="http://'+response.link+'">'+response.link+'</a><br>');
            //    if(listPosts.length > 0){
            //        i++;
            //        myEach(listPosts, i, maxLen);
            //    } else {
            //        alert("Rise Complete!");
            //    }
            //}).fail(function() {
            //    alert( "error" );
            //    console.log(response);
            //});
        }
        var i = 1;
        //var listPosts = jQuery('input.checkbox:checked'); console.log(listPosts);
        var listPosts = [];
        jQuery('input.checkbox:checked').each(function (i, elem) {
            listPosts.push(elem.value);
        });
        console.log(listPosts);
        var maxLen = listPosts.length;
        console.log(maxLen);
        myEach(listPosts, i, maxLen);



    });

    //закрыть окно терминала и очистить его
    jQuery(".terminal_close").click(function () {
        jQuery("#terminal").css("display", "none");
        jQuery("#progress_list").text("");
    });

    //выводит количество разных типов файлов
    jQuery("a#choice_html > span").text(jQuery(".choice_html.choice_rise").length + ' из ' + jQuery(".choice_html").length);
    jQuery("a#choice_htm > span").text(jQuery(".choice_htm.choice_rise").length + ' из ' + jQuery(".choice_htm").length);
    jQuery("a#choice_php > span").text(jQuery(".choice_php.choice_rise").length + ' из ' + jQuery(".choice_php").length);
    jQuery("a#choice_css > span").text(jQuery(".choice_css.choice_rise").length + ' из ' + jQuery(".choice_css").length);
    jQuery("a#choice_js > span").text(jQuery(".choice_js.choice_rise").length + ' из ' + jQuery(".choice_js").length);
    jQuery("a#choice_jpg > span").text(jQuery(".choice_jpg.choice_rise").length + ' из ' + jQuery(".choice_jpg").length);
    jQuery("a#choice_png > span").text(jQuery(".choice_png.choice_rise").length + ' из ' + jQuery(".choice_png").length);
    jQuery("a#choice_gif > span").text(jQuery(".choice_gif.choice_rise").length + ' из ' + jQuery(".choice_gif").length);
    jQuery("a#choice_pdf > span").text(jQuery(".choice_pdf.choice_rise").length + ' из ' + jQuery(".choice_pdf").length);
    jQuery("a#choice_else > span").text(jQuery(".choice_else.choice_rise").length + ' из ' + jQuery(".choice_else").length);
    jQuery("a#choice_rise > span").text(jQuery(".choice_rise").length);
    jQuery("a#choice_none > span").text(jQuery(".checkbox").length);

});