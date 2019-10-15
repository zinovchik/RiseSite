//<script type="text/javascript" src="http://rs-site.max/linksChecker.js"></script>

document.addEventListener("DOMContentLoaded", function (event) {

    var path_ajax_file = 'http://rs-site.max/linksChecker.php';

    var links_array = document.querySelectorAll('a');
    var img_array = document.querySelectorAll('img');

    var countLinks = links_array.length;
    var countImages = img_array.length;
    var countBedLinks = 0;
    var countBedImages = 0;
    var countBedAjax = 0;
    var countSendAjax = 0;
    var countBackAjax = 0;

    console.log(' * On page - Links: ' + countLinks + ', Images: ' + countImages);
    console.log(' * Please, Wait ... ');

    if (links_array) {
        for (var i = 0; i < links_array.length; i++) {

            if (links_array[i].href != '') {
                countSendAjax++;
                httpGet(path_ajax_file + "?url=" + links_array[i].href)
                    .then(
                    function (response) {
                        countBackAjax++;
                        if (response != '') {
                            countBedLinks++;
                            console.log('link: ' + response);
                        }
                        statusLinksChecker();
                    },
                    function (error) {
                        countBackAjax++;
                        countBedAjax++;
                        console.warn("Rejected: " + links_array[i].href);
                        statusLinksChecker();
                    }
                );
            }
        }
    }

    if(img_array) {
        for (var i = 0; i < img_array.length; i++) {

            if (img_array[i].src != '') {
                countSendAjax++;
                httpGet(path_ajax_file + "?url=" + img_array[i].src)
                    .then(
                    function (response) {
                        countBackAjax++;
                        if (response != '') {
                            countBedImages++;
                            console.log('img: ' + response);
                        }
                        statusLinksChecker();
                    },
                    function (error) {
                        countBackAjax++;
                        countBedAjax++;
                        console.info("Rejected: " + img_array[i].src);
                        statusLinksChecker();
                    }
                );
            }
        }
    }

    function statusLinksChecker(){
        if (countSendAjax == countBackAjax){
            console.log(' * Done! Broken links: ' + countBedLinks + '. Broken Images: ' + countBedImages + '. Errors Checking: ' + countBedAjax);
        }
    }

    function httpGet(url) {

        return new Promise(function (resolve, reject) {

            var xhr = new XMLHttpRequest();
            xhr.open('GET', url, true);

            xhr.onload = function () {
                if (this.status == 200) {
                    resolve(this.response);
                } else {
                    var error = new Error(this.statusText);
                    error.code = this.status;
                    reject(error);
                }
            };

            xhr.onerror = function () {
                reject(new Error("Network Error"));
            };

            xhr.send();
        });
    }
});
