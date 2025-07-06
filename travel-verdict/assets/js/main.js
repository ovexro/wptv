(function($) {
    'use strict';

    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
    }

    $(document).ready(function() {

        // --- Sticky Header ---
        const header = $('#masthead');
        const headerHeight = header.outerHeight();
        let lastScrollTop = 0;

        $(window).on('scroll', function() {
            let scrollTop = $(this).scrollTop();
            if (scrollTop > headerHeight) {
                header.addClass('sticky');
            } else {
                header.removeClass('sticky');
            }
            if (scrollTop > lastScrollTop && scrollTop > headerHeight){
                header.addClass('header-hidden');
            } else {
                if(scrollTop + $(window).height() < $(document).height()) {
                    header.removeClass('header-hidden');
                }
            }
            lastScrollTop = scrollTop;
        });

        // --- Initialize Liked Posts on Load ---
        const likedPostsCookie = getCookie('travel_verdict_liked_posts');
        if (likedPostsCookie) {
            try {
                const likedPosts = JSON.parse(likedPostsCookie);
                if (Array.isArray(likedPosts)) {
                    likedPosts.forEach(function(postId) {
                        $(`.like-button[data-post-id="${postId}"]`).addClass('liked');
                    });
                }
            } catch (e) {
                console.error('Could not parse liked posts cookie:', e);
            }
        }

        // --- Load More Posts ---
        $('#load-more-posts').on('click', function() {
            const button = $(this);
            const container = $('.posts-grid');
            button.text('Loading...').attr('disabled', true);
            $.ajax({
                url: tv_loadmore_params.ajax_url,
                data: {
                    action: 'load_more_posts',
                    query: tv_loadmore_params.posts,
                    page: tv_loadmore_params.current_page
                },
                type: 'POST',
                success: function(response) {
                    if (response) {
                        container.append(response);
                        tv_loadmore_params.current_page++;
                        if (tv_loadmore_params.current_page == tv_loadmore_params.max_page) {
                            button.hide();
                        }
                        button.text('Load More').attr('disabled', false);
                    } else {
                        button.hide();
                    }
                }
            });
        });

        // --- Distraction-Free Mode ---
        $('#distraction-free-toggle').on('click', function() {
            $('body').toggleClass('distraction-free-mode');
        });

        // --- Image Lightbox ---
        const lightbox = $('#tv-lightbox-overlay');
        const lightboxImage = $('#tv-lightbox-image');
        const lightboxClose = $('#tv-lightbox-close');
        $('.entry-content').on('click', 'a[href$=".jpg"], a[href$=".jpeg"], a[href$=".png"], a[href$=".gif"]', function(e) {
            e.preventDefault();
            const imageUrl = $(this).attr('href');
            lightboxImage.attr('src', imageUrl);
            lightbox.fadeIn(200);
            $('body').css('overflow', 'hidden');
        });
        function closeLightbox() {
            lightbox.fadeOut(200);
            $('body').css('overflow', 'auto');
        }
        lightboxClose.on('click', closeLightbox);
        lightbox.on('click', function(e) {
            if (e.target.id === 'tv-lightbox-overlay') {
                closeLightbox();
            }
        });

        // --- Font Size Controls ---
        const fontButtons = $('.font-size-button');
        const body = $('body');
        const savedSize = localStorage.getItem('travelVerdictFontSize');
        if (savedSize) {
            body.removeClass('font-size-small font-size-medium font-size-large').addClass('font-size-' + savedSize);
            fontButtons.removeClass('active');
            $('.font-size-button[data-size="' + savedSize + '"]').addClass('active');
        }
        fontButtons.on('click', function() {
            const size = $(this).data('size');
            body.removeClass('font-size-small font-size-medium font-size-large').addClass('font-size-' + size);
            fontButtons.removeClass('active');
            $(this).addClass('active');
            localStorage.setItem('travelVerdictFontSize', size);
        });

        // --- Back to Top Button ---
        const backToTopButton = $('#back-to-top');
        $(window).on('scroll', function() {
            if ($(this).scrollTop() > 300) {
                backToTopButton.addClass('show');
            } else {
                backToTopButton.removeClass('show');
            }
        });
        backToTopButton.on('click', function(e) {
            e.preventDefault();
            $('html, body').animate({ scrollTop: 0 }, 'smooth');
        });

        // --- Reading Progress Bar ---
        const progressBar = $('.reading-progress-bar');
        if (progressBar.length) {
            $(window).on('scroll', function() {
                const scrollTop = $(window).scrollTop();
                const docHeight = $(document).height();
                const winHeight = $(window).height();
                const scrollPercent = (scrollTop) / (docHeight - winHeight);
                const scrollPercentRounded = Math.round(scrollPercent * 100);
                progressBar.css('width', scrollPercentRounded + '%');
            });
        }

        // --- Mobile Menu Toggle ---
        $('#mobile-menu-toggle').on('click', function() {
            $('body').toggleClass('mobile-menu-open');
        });
        $('#mobile-menu-container .menu-item a').on('click', function() {
            $('body').removeClass('mobile-menu-open');
        });

        // --- Light/Dark Mode Switch ---
        const darkModeToggle = $('#dark-mode-toggle');
        const currentTheme = localStorage.getItem('theme');
        if (currentTheme) {
            $('html').attr('data-theme', currentTheme);
        }
        darkModeToggle.on('click', function() {
            let theme = $('html').attr('data-theme');
            if (theme === 'dark') {
                $('html').removeAttr('data-theme');
                localStorage.removeItem('theme');
            } else {
                $('html').attr('data-theme', 'dark');
                localStorage.setItem('theme', 'dark');
            }
        });

        // --- Search Toggle ---
        $('#search-toggle').on('click', function() {
            $('.search-form-container').toggleClass('active');
            if ($('.search-form-container').hasClass('active')) {
                $('.search-form-container .search-field').focus();
            }
        });
        
        // --- Live Search ---
        let searchTimer;
        const searchInput = $('.search-form-container .search-field');
        const resultsContainer = $('#live-search-results');
        searchInput.on('keyup', function() {
            const query = $(this).val();
            clearTimeout(searchTimer);
            if (query.length < 3) {
                resultsContainer.hide().empty();
                return;
            }
            searchTimer = setTimeout(function() {
                resultsContainer.html('<div class="live-search-loading">Loading...</div>').show();
                $.ajax({
                    url: tv_ajax.ajax_url, type: 'POST', data: { action: 'travel_verdict_ajax_search', nonce: tv_ajax.search_nonce, query: query },
                    success: function(response) {
                        resultsContainer.empty();
                        if (response.success && response.data.length) {
                            response.data.forEach(function(item) {
                                const resultItem = `<a href="${item.permalink}" class="live-search-item"><img src="${item.thumbnail}" alt="" class="live-search-thumbnail" width="50" height="50"><span class="live-search-title">${item.title}</span></a>`;
                                resultsContainer.append(resultItem);
                            });
                        } else {
                            resultsContainer.html('<div class="live-search-no-results">No results found.</div>');
                        }
                    }
                });
            }, 500);
        });
        $(document).on('click', function(event) {
            if (!$(event.target).closest('.search-form-container').length) {
                resultsContainer.hide().empty();
            }
        });

        // --- Print Post Button ---
        $('#print-post-button').on('click', function() {
            window.print();
        });

        // --- Comments Toggle ---
        $('.comments-toggle').on('click', function() {
            $('#comments-section').slideToggle();
        });

        // --- Copy Link Icon ---
        $('.copy-link-button').on('click', function(e) {
            e.preventDefault();
            const postUrl = $(this).data('url');
            const button = $(this);
            navigator.clipboard.writeText(postUrl).then(function() {
                button.addClass('success');
                setTimeout(function() { button.removeClass('success'); }, 2000);
            }, function(err) {
                console.error('Could not copy text: ', err);
                alert('Failed to copy link.');
            });
        });

        // --- Share Icon ---
        $('.share-button').on('click', function(e) {
            e.stopPropagation();
            $(this).parent('.share-feature').toggleClass('active');
        });
        $(document).on('click', function(event) {
            if (!$(event.target).closest('.share-feature').length) {
                $('.share-feature').removeClass('active');
            }
        });

        // --- Like Button ---
        $('.like-button').on('click', function() {
            const button = $(this);
            const postId = button.data('post-id');
            const likeCountSpan = button.find('.like-count');
            if (button.hasClass('liked')) return;
            $.ajax({
                url: tv_ajax.ajax_url, type: 'post', data: { action: 'travel_verdict_like_post', post_id: postId, nonce: tv_ajax.like_nonce },
                success: function(response) {
                    if (response.success) {
                        likeCountSpan.text(response.data.likes);
                        button.addClass('liked');
                    } else {
                        alert(response.data.message);
                        button.addClass('liked');
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Like request failed: ' + textStatus, errorThrown);
                    alert('An error occurred while liking the post. Please try again.');
                }
            });
        });

        // --- Cookie Consent ---
        const consentBanner = $('#cookie-consent-banner');
        const consentAcceptBtn = $('#cookie-consent-accept');
        const consentCookie = getCookie('tv_cookie_consent');
        if (!consentCookie) {
            consentBanner.addClass('show-consent');
        }
        consentAcceptBtn.on('click', function() {
            let d = new Date();
            d.setTime(d.getTime() + (365 * 24 * 60 * 60 * 1000));
            let expires = "expires=" + d.toUTCString();
            document.cookie = "tv_cookie_consent=true;" + expires + ";path=/";
            consentBanner.removeClass('show-consent');
        });

        // --- Table of Contents Smooth Scroll ---
        $(document.body).on('click', '.toc-link', function(e) {
            e.preventDefault();
            const target = $(this.hash);
            if (target.length) {
                const headerOffset = $('#masthead').hasClass('sticky') ? $('#masthead').outerHeight() : 0;
                $('html, body').animate({
                    scrollTop: target.offset().top - headerOffset - 20
                }, 800);
            }
        });
    });

})(jQuery);
