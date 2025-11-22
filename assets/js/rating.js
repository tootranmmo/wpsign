/**
 * 5-Star Rating System
 *
 * @package AdPrint_Blog
 * @since 1.0.0
 */

(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        const ratingContainers = document.querySelectorAll('.rating-interactive');

        ratingContainers.forEach(function(container) {
            const stars = container.querySelectorAll('.rating-star');
            const postId = container.dataset.postId;
            const currentRating = container.dataset.currentRating || 0;
            const ratingDisplay = container.querySelector('.rating-value');
            const ratingCount = container.querySelector('.rating-count');

            let selectedRating = 0;

            // Initialize
            updateStars(currentRating);

            stars.forEach(function(star, index) {
                const starValue = index + 1;

                // Mouse hover
                star.addEventListener('mouseenter', function() {
                    updateStars(starValue, true);
                });

                // Mouse leave
                star.addEventListener('mouseleave', function() {
                    updateStars(selectedRating || currentRating);
                });

                // Click to rate
                star.addEventListener('click', function() {
                    selectedRating = starValue;
                    submitRating(postId, starValue, container);
                });

                // Keyboard accessibility
                star.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        selectedRating = starValue;
                        submitRating(postId, starValue, container);
                    }
                });
            });

            /**
             * Update star display
             */
            function updateStars(rating, isHover = false) {
                stars.forEach(function(star, index) {
                    const starValue = index + 1;

                    if (starValue <= rating) {
                        star.classList.add('active');
                        star.classList.remove('inactive');

                        if (isHover) {
                            star.classList.add('hover');
                        } else {
                            star.classList.remove('hover');
                        }
                    } else {
                        star.classList.remove('active', 'hover');
                        star.classList.add('inactive');
                    }
                });

                // Update display
                if (ratingDisplay) {
                    ratingDisplay.textContent = rating.toFixed(1);
                }
            }

            /**
             * Submit rating via AJAX
             */
            function submitRating(postId, rating, container) {
                // Check if already rated
                if (localStorage.getItem('rated_post_' + postId)) {
                    showNotification('You have already rated this post', 'warning', container);
                    return;
                }

                // Show loading state
                container.classList.add('rating-loading');

                // Send AJAX request
                fetch(adprintData.ajaxUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        action: 'adprint_submit_rating',
                        post_id: postId,
                        rating: rating,
                        nonce: adprintData.nonce,
                    }),
                })
                .then(function(response) {
                    return response.json();
                })
                .then(function(data) {
                    container.classList.remove('rating-loading');

                    if (data.success) {
                        // Update display
                        updateStars(data.data.average_rating);

                        if (ratingDisplay) {
                            ratingDisplay.textContent = data.data.average_rating.toFixed(1);
                        }

                        if (ratingCount) {
                            ratingCount.textContent = '(' + data.data.rating_count + ')';
                        }

                        // Save to localStorage to prevent duplicate ratings
                        localStorage.setItem('rated_post_' + postId, rating);

                        // Show success message
                        showNotification('Thank you for rating!', 'success', container);

                        // Disable further rating
                        stars.forEach(function(star) {
                            star.style.pointerEvents = 'none';
                        });
                    } else {
                        showNotification(data.data || 'Error submitting rating', 'error', container);
                    }
                })
                .catch(function(error) {
                    container.classList.remove('rating-loading');
                    showNotification('Error submitting rating', 'error', container);
                    console.error('Rating error:', error);
                });
            }

            /**
             * Show notification
             */
            function showNotification(message, type, container) {
                const notification = document.createElement('div');
                notification.className = 'rating-notification rating-notification-' + type;
                notification.textContent = message;
                notification.setAttribute('role', 'alert');

                container.appendChild(notification);

                setTimeout(function() {
                    notification.classList.add('show');
                }, 10);

                setTimeout(function() {
                    notification.classList.remove('show');
                    setTimeout(function() {
                        container.removeChild(notification);
                    }, 300);
                }, 3000);
            }
        });

        /**
         * Animated rating count up
         */
        const animatedRatings = document.querySelectorAll('.rating-value[data-animate]');

        animatedRatings.forEach(function(element) {
            const finalValue = parseFloat(element.textContent);
            const duration = 1000; // 1 second
            const steps = 60;
            const stepValue = finalValue / steps;
            let currentValue = 0;
            let currentStep = 0;

            const interval = setInterval(function() {
                currentStep++;
                currentValue += stepValue;

                if (currentStep >= steps) {
                    element.textContent = finalValue.toFixed(1);
                    clearInterval(interval);
                } else {
                    element.textContent = currentValue.toFixed(1);
                }
            }, duration / steps);
        });
    });

})();

// AJAX handler for rating submission (add to functions.php)
/*
function adprint_submit_rating_ajax() {
    check_ajax_referer('adprint-nonce', 'nonce');

    $post_id = intval($_POST['post_id']);
    $rating = floatval($_POST['rating']);

    if ($post_id <= 0 || $rating < 1 || $rating > 5) {
        wp_send_json_error('Invalid parameters');
    }

    // Get current ratings
    $ratings = get_post_meta($post_id, 'adprint_ratings', true);

    if (!is_array($ratings)) {
        $ratings = array(
            'total' => 0,
            'count' => 0,
        );
    }

    // Add new rating
    $ratings['total'] += $rating;
    $ratings['count']++;

    // Calculate average
    $average = $ratings['total'] / $ratings['count'];

    // Update meta
    update_post_meta($post_id, 'adprint_ratings', $ratings);
    update_post_meta($post_id, 'adprint_rating', $average);

    wp_send_json_success(array(
        'average_rating' => $average,
        'rating_count' => $ratings['count'],
    ));
}
add_action('wp_ajax_adprint_submit_rating', 'adprint_submit_rating_ajax');
add_action('wp_ajax_nopriv_adprint_submit_rating', 'adprint_submit_rating_ajax');
*/
