document.addEventListener('DOMContentLoaded', function() {
    console.log('Custom AJAX Script Loaded!');

    // 1. UI එකට බටන් එකක් සහ ඩේටා පෙන්වන ඩිව් (div) එකක් එක් කිරීම (අපි තාවකාලිකව JS මඟින්ම දාමු)
    const contentMain = document.querySelector('.site-content');
    if (contentMain) {
        const ajaxSection = document.createElement('div');
        ajaxSection.innerHTML = `
            <div style="margin: 30px 0; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                <h3>🚀 AJAX Dynamic Portfolio Loader</h3>
                <button id="load-portfolios-btn" style="background: #27ae60; color: #fff; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer;">Load Portfolios via AJAX</button>
                <div id="ajax-portfolio-results" style="margin-top: 15px;"></div>
            </div>
        `;
        contentMain.appendChild(ajaxSection);

        // 2. බටන් එක ක්ලික් කළ විට ක්‍රියාත්මක වන කෝඩ් එක (Event Listener)
        const loadBtn = document.getElementById('load-portfolios-btn');
        const resultsDiv = document.getElementById('ajax-portfolio-results');

        loadBtn.addEventListener('click', function() {
            loadBtn.innerText = 'Loading...';

            // WordPress AJAX Request යැවීම (jQuery මඟින්)
            jQuery.ajax({
                url: my_ajax_obj.ajax_url,
                type: 'POST',
                data: {
                    action: 'load_more_portfolios'
                },
                success: function(response) {
                    loadBtn.innerText = 'Load Portfolios via AJAX';
                    if(response.success && response.data.length > 0) {
                        let html = '<ul style="list-style: none; padding: 0;">';
                        response.data.forEach(function(item) {
                            html += `<li style="background: #f9f9f9; margin-bottom: 10px; padding: 10px; border-left: 3px solid #2980b9;">
                                        <strong><a href="${item.link}" target="_blank">${item.title}</a></strong>
                                        <p style="margin: 5px 0 0 0; font-size: 0.9rem; color: #666;">${item.excerpt}</p>
                                     </li>`;
                        });
                        html += '</ul>';
                        resultsDiv.innerHTML = html;
                    } else {
                        resultsDiv.innerHTML = '<p>No portfolios found.</p>';
                    }
                },
                error: function() {
                    loadBtn.innerText = 'Load Portfolios via AJAX';
                    resultsDiv.innerHTML = '<p style="color: red;">Error loading data.</p>';
                }
            });
        });
    }
});



