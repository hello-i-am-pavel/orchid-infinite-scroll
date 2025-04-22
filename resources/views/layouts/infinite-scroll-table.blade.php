@include('platform::layouts.table', [
    'columns' => $columns,
    'rows'    => $rows,
])
<div id="load-more-spinner" style="display: none;" class="text-center p-3">
    <em>Загрузка...</em>
</div>

<script>
    const infinityScroll = function () {
        let page =  parseInt(new URLSearchParams(window.location.search).get('page')) || 1;
        let loading = false;
        let hasMore = true;
        let userHasScrolledSinceLastLoad = false;

        const table = document.querySelector('table');
        const tbody = table?.querySelector('tbody');
        const spinner = document.getElementById('load-more-spinner');

        async function loadPage(targetPage) {
            if (loading || !hasMore) return;

            loading = true;
            spinner.style.display = 'block';

            const url = new URL(window.location.href);
            url.searchParams.set('page', targetPage);

            try {
                const response = await fetch(url.toString(), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                const html = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTbody = doc.querySelector('tbody');
                const newRows = Array.from(newTbody?.querySelectorAll('tr') ?? []);

                const meaningfulRows = newRows.filter(row => {
                    const cells = Array.from(row.querySelectorAll('td'));
                    const text = cells.map(td => td.textContent?.trim()).join('');
                    return text && !/no\s+records\s+found/i.test(text);
                });

                if (meaningfulRows.length > 0) {
                    meaningfulRows.forEach(row => {
                        tbody.appendChild(row);
                    });

                    page = targetPage;
                    userHasScrolledSinceLastLoad = false;

                    const newUrl = new URL(window.location.href);
                    newUrl.searchParams.set('page', page);
                    window.history.replaceState({}, '', newUrl.toString());
                } else {
                    hasMore = false;
                    window.removeEventListener('scroll', onScroll);
                }
            } catch (e) {
                console.error('Infinite scroll fetch error:', e);
            } finally {
                loading = false;
                spinner.style.display = 'none';
            }
        }

        function onScroll() {
            const rect = table?.getBoundingClientRect();
            const reachedBottom = rect && rect.bottom <= window.innerHeight;

            if (reachedBottom && !loading && hasMore && userHasScrolledSinceLastLoad) {
                loadPage(page + 1);
            }

            if (!reachedBottom) {
                userHasScrolledSinceLastLoad = true;
            }
        }

        window.addEventListener('scroll', onScroll);
    };

    document.addEventListener('DOMContentLoaded', infinityScroll);
    document.addEventListener('turbo:load', infinityScroll);
</script>
