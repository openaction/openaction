import React from 'react';
import redaxios from 'redaxios';
import { render, screen } from '@testing-library/react';
import '@testing-library/jest-dom';
import { Tag, TagsRegistry } from '../../../../../react/components/Layout/TagsSelector/Model/tag';
import { MultipleTagsSelector } from '../../../../../react/components/Layout/TagsSelector/MultipleTagSelector';
import { mockTagsApiResponse } from '../../../../fixtures/tags';

jest.mock('redaxios');
(redaxios.request as any).mockImplementation(() => mockTagsApiResponse());

describe('MultipleTagsSelector', () => {
    it('defaultValue=none', async () => {
        let selected: Tag[] = [];

        render(
            <div role="tag-selector">
                <MultipleTagsSelector
                    tagsRegistry={new TagsRegistry('http://localhost')}
                    placeholder="Select tags"
                    noResultsLabel="No result"
                    onChange={(t) => (selected = t)}
                />
            </div>
        );

        expect(await screen.findByRole('tag-selector')).toBeInTheDocument();
        expect(selected).toStrictEqual([]);

        // The placeholder should be displayed
        const input = screen.getByRole('tag-selector').querySelector('input');
        expect(input).not.toBeDisabled();
        expect(input.getAttribute('placeholder')).toBe('Select tags');

        // TODO: Check onChange
    });

    it('defaultValue=[6, 3]', async () => {
        let selected: Tag[] = [];

        render(
            <div role="tag-selector">
                <MultipleTagsSelector
                    tagsRegistry={new TagsRegistry('http://localhost')}
                    placeholder="Select tags"
                    noResultsLabel="No result"
                    defaultValue={[6, 3]}
                    onChange={(t) => (selected = t)}
                />
            </div>
        );

        expect(await screen.findByRole('tag-selector')).toBeInTheDocument();
        const container = screen.getByRole('tag-selector');

        // The input shouldn't have a placeholder
        const input = container.querySelector('input');
        expect(input).not.toBeDisabled();
        expect(input.getAttribute('placeholder')).toBeNull();

        // Check default displayed tags
        let displayedTags = [];
        container.querySelectorAll('.bp4-tag .bp4-text-overflow-ellipsis').forEach((node) => {
            displayedTags.push(node.innerHTML);
        });

        expect(displayedTags).toStrictEqual(['contains tag keyword lowercase', 'StartWithTag']);

        // TODO: Check onChange
    });
});
