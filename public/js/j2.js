$('document').ready(function() {

    var current = {
        token: 'null',
        event: null,
        announcement: null,
        jobOrder: null
    };
    var sliderTemplate = $('#sliderTemplate').html();
    var slideTemplate = $('#slideTemplate').html();
    var videoTemplate = $('#videoTemplate').html();
    var itemTemplate = $('#itemTemplate').html();
    var announcementMarqueeOnTemplate = $('#announcementMarqueeOnTemplate').html();
    var announcementMarqueeOffTemplate = $('#announcementMarqueeOffTemplate').html();
    var loopInterval = 1000;

    loop();
    // getBoard();

    function loop() {
        $.get({
            url: '/checker/monitor.php',
            success: function(res) {
                try {
                    if(res.token != current.token) {
                        // something happened

                        if(res.action == 'idle') {
                            getBoard();
                            loopInterval = 1500;
                        } else if(res.action == 'hasQue') {
                            getJO(res.transaction_id);
                            loopInterval = 500;
                            setTimeout(clearMonitor, 30000);
                        } else if(res.action == 'select-customer') {
                            loopInterval = 500;
                            getCustomer(res.token);
                            clearItems();
                        }
                        current.token = res.token;
                        getAnnouncement();
                        console.log('action', res.action)
                    }

                    setTimeout(loop, loopInterval);
                } catch (error) {
                    alert(error);
                }
            },
            error: function(err) {
                console.log('failed checking changes. Retry in 20 seconds', err);
                setTimeout(loop, 20000);
            }
        });
    }

    function clearMonitor() {
        $.post({
            url: '/api/boards/clear-monitor'
        });
    }

    function getBoard() {
        $('.jo-wrapper').hide(500);
        $('.board-wrapper').fadeIn(2500);
        $.get({
            url: '/api/boards/today',
            success: function(res) {
                try {
                    if(res.event.event_type_id == 1) {
                        var slides = res.event.slides;
            
                        var slider = sliderTemplate.replace(/{{slides}}/, toHtmlSlider(slides));
            
                        $('#boardContent').html(slider);
            
                        $('.slider_circle_10').EasySlides({
                            autoplay: true,
                            'timeout': 8000,
                            'show': slides.length,
                            // 'vertical': false,
                            // 'reverse': false,
                            // 'touchevents': true,
                            // 'delayaftershow': 300,
                            // 'stepbystep': true,
                            // 'startslide': 0,
                            // 'loop': true,
                            // 'distancetochange': 15,
                            // 'beforeshow': function () {},
                            // 'aftershow': function () {},
                        })
                    } else if(res.event.event_type_id == 2) {
                        if(res.event.video) {
                            var video = videoTemplate.replace(/{{source}}/, res.event.video.source);
                            $('#boardContent').html(video);
                            $('#videoFile')[0].play();
                        }
                    }
                } catch (error) {
                    alert(error);
                }
            },
            error: function(err) {
                console.log('failed to load board. Retry in 20 seconds', err);
                setTimeout(getBoard, 20000);
            }
        });
    }

    function toHtmlSlide(slide) {
        return slideTemplate.replace(/{{source}}/, slide.source);
    }

    function toHtmlSlider(slides) {
        var htmlSlide = '';
        for (var index = 0; index < slides.length; index++) {
            var slide = slides[index];
            htmlSlide += toHtmlSlide(slide);
        }
        return htmlSlide;
    }

    function toHtmlItem(item) {
        return itemTemplate.replace(/{{name}}/, item.name)
            .replace(/{{quantity}}/, item.quantity)
            .replace(/{{unit_price}}/, item.unit_price)
            .replace(/{{total_price}}/, item.total_price);
    }

    function toHtmlItemKg(item) {
        return itemTemplate.replace(/{{name}}/, item.name)
            .replace(/{{quantity}}/, item.kilos + 'KG')
            .replace(/{{unit_price}}/, item.price_per_kilo)
            .replace(/{{total_price}}/, item.total_price);
    }

    function toHtmlAnnouncement(announcement) {
        if(announcement.marquee_on) {
            return announcementMarqueeOnTemplate.replace(/{{announcementContent}}/, announcement.content);
        } else {
            return announcementMarqueeOffTemplate.replace(/{{announcementContent}}/, announcement.content);
        }
    }

    function getAnnouncement() {
        $.get({
            url: '/api/boards/announcement',
            success: function(res) {
                $('#announcement').html(toHtmlAnnouncement(res.announcement));
            },
            error: function(err) {
                console.log('Failed to load announcement. Retry in 20 seconds', err);
                setTimeout(getAnnouncement, 20000);
            }
        });
    }

    function getJO(joId) {
        $('.jo-wrapper').show(500);
        $('.board-wrapper').hide(500);
        $.get({
            url: '/api/boards/job-order/' + joId,
            success: function(res) {
                setCustomer(res.transaction.customer)
                setItems(res.transaction);
                console.log(res)
            }
        })
    }

    function getCustomer(customerId) {
        $('.jo-wrapper').show(500);
        $('.board-wrapper').hide(500);
        $.get({
            url: '/api/boards/' + customerId,
            success: function(res) {
                setCustomer(res.customer)
                console.log(res)
            }
        })
    }

    function setCustomer(customer) {
        $('#customerName').text('[CRN#' + customer.crn + '] - ' + customer.name);
    }

    function setItems(transaction) {
        var services = '';
        for (var index = 0; index < transaction.posServiceItems.length; index++) {
            var service = transaction.posServiceItems[index];
            services += toHtmlItem(service)
        }
        
        for (var index = 0; index < transaction.posLagoonItems.length; index++) {
            var service = transaction.posLagoonItems[index];
            services += toHtmlItem(service)
        }

        for (var index = 0; index < transaction.posLagoonPerKiloItems.length; index++) {
            var service = transaction.posLagoonPerKiloItems[index];
            services += toHtmlItemKg(service)
        }

        for (var index = 0; index < transaction.posScarpaCleaningItems.length; index++) {
            var service = transaction.posScarpaCleaningItems[index];
            services += toHtmlItem(service)
        }

        for (var index = 0; index < transaction.posProductItems.length; index++) {
            var service = transaction.posProductItems[index];
            services += toHtmlItem(service)
        }

        $('#items>tbody').hide().html(services).fadeIn(100);
        $('td#totalPrice>span').html(transaction.total_price)
        $('#items').fadeIn(1000);
        $('#itemsPlaceholder').fadeOut(200);
    }

    function clearItems() {
        $('#itemsPlaceholder').fadeIn(1000);
        $('#items').hide();
        $('#items>tbody').html("");
        $('td#totalPrice>span').html("")
    }

    setInterval(function() {
        var date = new Date();
        var hours = date.getHours();
        var minutes = date.getMinutes();
        var ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12; // the hour '0' should be '12'
        minutes = minutes < 10 ? '0'+minutes : minutes;
        var strTime = hours + ':' + minutes + ' ' + ampm;
        $('#time').text(strTime);

        $('#date').text(date.toDateString());
    }, 1000);
});
