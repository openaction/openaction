import { Controller } from '@hotwired/stimulus';
import { MeiliSearch } from 'meilisearch';

export default class extends Controller {
    static values = {
        endpoint: String,
        key: String,
        project: String,
        type: String,
        sort: String,
        limit: Number,
    }

    static targets = [
        'queryInput',
        'categoryFilter',
        'resultTemplate',
        'resultsList',
    ];

    connect() {
        const client = new MeiliSearch({ host: this.endpointValue, apiKey: this.keyValue });
        const index = client.index('public');

        const template = this.parseTemplate();

        this.refreshQuery(index, template);

        if (this.hasQueryInputTarget) {
            this.queryInputTarget.addEventListener('input', () => this.refreshQuery(index, template));
        }

        if (this.hasCategoryFilterTarget) {
            for (let i in this.categoryFilterTargets) {
                this.categoryFilterTargets[i].addEventListener('change', () => this.refreshQuery(index, template));
            }
        }
    }

    parseTemplate() {
        const txt = document.createElement('textarea');
        txt.innerHTML = this.resultTemplateTarget.innerHTML;

        return txt.value;
    }

    refreshQuery(index, template) {
        let query = '';
        if (this.hasQueryInputTarget) {
            query = this.queryInputTarget.value;
        }

        let categories = [];
        if (this.hasCategoryFilterTarget) {
            for (let i in this.categoryFilterTargets) {
                if (this.categoryFilterTargets[i].value && this.categoryFilterTargets[i].checked) {
                    categories.push(this.categoryFilterTargets[i].value);
                }
            }
        }

        if (this.element.timeout) {
            clearTimeout(this.element.timeout);
        }

        this.element.timeout = setTimeout(() => this.search(index, template, query, categories), 500);
    }

    search(index, template, query, categories) {
        this.element.classList.add('searchengine-loading');

        let filters = [];
        for (let i in categories) {
            filters.push('categories = "'+ categories[i] +'"')
        }

        if (this.hasProjectValue) {
            filters.push('restrictions_projects = "'+ this.projectValue +'"');
        }

        if (this.hasTypeValue) {
            filters.push('type = "'+ this.typeValue +'"');
        }

        index
            .search(query, {
                filter: filters,
                limit: this.hasLimitValue ? this.limitValue : 25,
                sort: this.hasSortValue ? this.sortValue.split(',') : undefined,
            })
            .then((response) => {
                this.element.classList.remove('searchengine-loading');
                this.displayResults(response.hits, template);
            });
    }

    displayResults(items, template) {
        let html = '';
        for (let i in items) {
            html += ejs.render(template, { item: items[i] });
        }

        this.resultsListTarget.innerHTML = html;
    }
}
