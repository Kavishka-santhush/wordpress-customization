document.addEventListener('DOMContentLoaded', function() {
    console.log('Custom AJAX Script Loaded!');

    // උදාහරණයක් ලෙස කන්සෝල් එකට හෝ බටන් එකකට ඩේටා ගෙන්වා ගැනීම
    // මෙතනදී අපි jQuery හෝ Vanilla JS fetch පාවිච්චි කරන්න පුළුවන්. WordPress සාමාන්‍යයෙන් jQuery සමඟ එන නිසා අපි ඒක පාවිච්චි කරමු:
    
    /* 
    jQuery.ajax({
        url: my_ajax_obj.ajax_url,
        type: 'POST',
        data: {
            action: 'load_more_portfolios'
        },
        success: function(response) {
            if(response.success) {
                console.log('Fetched Portfolios via AJAX:', response.data);
            }
        }
    });
    */
});