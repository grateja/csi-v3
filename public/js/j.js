$('document').ready(function() {

    var current = {
        token: null,
        event: null,
        announcement: null,
        jobOrder: null
    };
    var sliderTemplate = $('#sliderTemplate').html();
    var slideTemplate = $('#slideTemplate').html();
    var videoTemplate = $('#videoTemplate').html();

    getKeys();
    getAnnouncement();
    getBoard();

    function getKeys() {
        $.get({
            url: '/checker/monitor.php',
            success: function(res) {
                try {
                    if(res.token != current.token) {
                        // something happened

                        if(res.action == 'iddle') {
                            getBoard();
                        } else if(res.action == 'hasQue') {
                            getJO();
                        }
                        current.token = res.token;
                    }
                    // if(current.board != res.board) {
                    //     // board changed
                    //     console.log('board changed', res.board);
                    //     getBoard();
                    // }

                    if(current.announcement != res.announcement) {
                        // announcement changed
                        console.log('announcement changed', res.announcement);
                        getAnnouncement();
                    }

                    current.announcement = res.announcement;
                    // current.board = res.board;

                    setTimeout(getKeys, 1000);
                } catch (error) {
                    alert(error);
                }
            },
            error: function(err) {
                console.log('failed checking changes. Retry in 20 seconds', err);
                setTimeout(getKeys, 20000);
            }
        });
    }

    function getBoard() {
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
        console.log('slide', slide)
        return slideTemplate.replace(/{{source}}/, slide.source);
    }

    function toHtmlSlider(slides) {
        console.log('slides', slides);
        var htmlSlide = '';
        for (var index = 0; index < slides.length; index++) {
            var slide = slides[index];
            htmlSlide += toHtmlSlide(slide);
        }
        return htmlSlide;
    }

    function getAnnouncement() {
        $.get({
            url: '/api/boards/announcement',
            success: function(res) {
                $('#announcementContent').text(res.announcement.content);
            },
            error: function(err) {
                console.log('Failed to load announcement. Retry in 20 seconds', err);
                setTimeout(getAnnouncement, 20000);
            }
        });
    }

    function getJO(joId) {

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
