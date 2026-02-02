<div id="infinite-scroll-table" data-infinite-scroll>
    @include('platform::layouts.table', [
        'columns' => $columns,
        'rows'    => $rows,
    ])
    <div id="load-more-spinner" style="display: none;" class="text-center p-3">
        <em>{{ __('Loading...') }}</em>
    </div>
    <div class="text-center p-3">
        <button type="button" id="load-more-button" class="btn icon-link btn-secondary>
            {{ __('Load more') }}
        </button>
    </div>
</div>

<script>
    const infinityScroll = function () {
        const container = document.querySelector('[data-infinite-scroll]');
        if (!container) return;

        let page = parseInt(new URLSearchParams(window.location.search).get('page')) || 1;
        let loading = false;
        let hasMore = true;
        let userHasScrolledSinceLastLoad = false;

        const table = container.querySelector('table');
        const tbody = table?.querySelector('tbody');
        const spinner = container.querySelector('#load-more-spinner');
        const loadMoreButton = container.querySelector('#load-more-button');

        function stopLoadingMore() {
            hasMore = false;
            window.removeEventListener('scroll', onScroll);
            if (loadMoreButton) {
                loadMoreButton.disabled = true;
                loadMoreButton.style.display = 'none';
            }
        }

        async function loadPage(targetPage) {
            if (loading || !hasMore || !tbody) return;

            loading = true;
            if (spinner) {
                spinner.style.display = 'block';
            }

            const url = new URL(window.location.href);
            url.searchParams.set('page', targetPage);

            try {
                const response = await fetch(url.toString(), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                const html = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContainer = doc.querySelector('[data-infinite-scroll]');
                const newTbody = newContainer?.querySelector('tbody');
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
                    stopLoadingMore();
                }
            } catch (e) {
                console.error('Infinite scroll fetch error:', e);
            } finally {
                loading = false;
                if (spinner) {
                    spinner.style.display = 'none';
                }
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

        if (loadMoreButton) {
            loadMoreButton.addEventListener('click', () => loadPage(page + 1));
        }

        window.addEventListener('scroll', onScroll);
    };

    document.addEventListener('DOMContentLoaded', infinityScroll);
    document.addEventListener('turbo:load', infinityScroll);
</script>
