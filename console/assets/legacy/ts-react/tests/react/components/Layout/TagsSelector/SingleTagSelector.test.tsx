import React from 'react';
import redaxios from 'redaxios';
import { fireEvent, render, screen } from '@testing-library/react';
import '@testing-library/jest-dom';
import { SingleTagSelector } from '../../../../../react/components/Layout/TagsSelector/SingleTagSelector';
import { Tag, TagsRegistry } from '../../../../../react/components/Layout/TagsSelector/Model/tag';
import { mockTagsApiResponse } from '../../../../fixtures/tags';

jest.mock('redaxios');
(redaxios.request as any).mockImplementation(() => mockTagsApiResponse());

describe('SingleTagSelector', () => {
    it('defaultValue=none', async () => {
        let selected: Tag | null = null;

        render(
            <div role="tag-selector">
                <SingleTagSelector
                    tagsRegistry={new TagsRegistry('http://localhost')}
                    placeholder="Select a tag"
                    noResultsLabel="No result"
                    onChange={(t) => (selected = t)}
                />
            </div>
        );

        expect(await screen.findByRole('tag-selector')).toBeInTheDocument();
        expect(selected).toBeNull();

        // The button label should be the placeholder
        const button = screen.getByRole('tag-selector').querySelector('button');
        expect(button).not.toBeDisabled();
        expect(button.querySelector('.bp4-button-text').innerHTML).toBe('Select a tag');

        // Select other tag
        fireEvent.click(button);
        fireEvent.click(screen.getByText('ContainsTagInside'));

        expect(screen.getByRole('tag-selector').querySelector('button')).not.toBeDisabled();
        expect(screen.getByRole('tag-selector').querySelector('button .bp4-button-text').innerHTML).toBe(
            'ContainsTagInside'
        );
        expect(selected ? selected.name : '').toBe('ContainsTagInside');
    });

    it('defaultValue=6', async () => {
        let selected: Tag | null = null;

        render(
            <div role="tag-selector">
                <SingleTagSelector
                    tagsRegistry={new TagsRegistry('http://localhost')}
                    placeholder="Select a tag"
                    noResultsLabel="No result"
                    defaultValue={6}
                    onChange={(t) => (selected = t)}
                />
            </div>
        );

        expect(await screen.findByRole('tag-selector')).toBeInTheDocument();
        expect(selected ? selected.name : '').toBe('contains tag keyword lowercase');

        // The button label should be the default tag
        const button = screen.getByRole('tag-selector').querySelector('button');
        expect(button).not.toBeDisabled();
        expect(button.querySelector('.bp4-button-text').innerHTML).toBe('contains tag keyword lowercase');

        // Select other tag
        fireEvent.click(button);
        fireEvent.click(screen.getByText('ContainsTagInside'));

        expect(screen.getByRole('tag-selector').querySelector('button')).not.toBeDisabled();
        expect(screen.getByRole('tag-selector').querySelector('button .bp4-button-text').innerHTML).toBe(
            'ContainsTagInside'
        );
        expect(selected ? selected.name : '').toBe('ContainsTagInside');
    });
});
