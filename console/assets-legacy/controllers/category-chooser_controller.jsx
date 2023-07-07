import React from 'react';
import { render } from 'react-dom';
import { Controller } from 'stimulus';
import { translator } from '../services/translator';

export default class extends Controller {
    connect() {
        const handleChange = (option) => {
            location.href = this.element.getAttribute('data-endpoint') + '?c=' + option.target.value;
        };

        const categories = JSON.parse(this.element.getAttribute('data-categories'));
        const currentCategory = this.element.getAttribute('data-selected');

        render(
            <select className="form-control" onChange={handleChange} value={currentCategory}>
                <option key="0" value="0">
                    {translator.trans('category-chooser.all_categories')}
                </option>
                {categories.map((category) => {
                    return (
                        <option key={category.id} value={category.id}>
                            {category.name}
                        </option>
                    );
                })}
            </select>,
            this.element
        );
    }
}
