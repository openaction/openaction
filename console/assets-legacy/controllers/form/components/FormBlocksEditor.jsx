import React, { useState } from 'react';
import { MetadataView } from './MetadataView';
import { BlockView } from './BlockView';
import { blockBuilder } from '../blocks/BlockBuilder';
import { httpClient } from '../../../services/http-client';
import { AddBlockButton } from './AddBlockButton';
import { SortableContainer, SortableElement, SortableHandle } from 'react-sortable-hoc';
import { arrayMoveImmutable } from 'array-move';

let saveTimeout = null;

const DragHandle = SortableHandle(() => (
    <div className="form-block-grip">
        <i className="fas fa-grip-horizontal" />
    </div>
));

const SortableItem = SortableElement(({ value, focused, setFocused, changeBlock, deleteBlock, duplicateBlock }) => (
    <div
        className={'form-block mb-3 ' + (focused === value.id ? 'form-block-focused' : '')}
        onClick={() => setFocused(value.id)}
    >
        <BlockView
            block={value}
            dragHandle={<DragHandle />}
            focused={focused === value.id}
            onChange={(newBlock) => changeBlock(value, newBlock)}
            onDelete={(block) => deleteBlock(block)}
            onDuplicate={(block) => duplicateBlock(block)}
        />
    </div>
));

const SortableList = SortableContainer(({ items, focused, setFocused, changeBlock, deleteBlock, duplicateBlock }) => {
    return (
        <div>
            {items.map((block, index) => (
                <SortableItem
                    key={`block-${block.id}`}
                    index={index}
                    value={block}
                    focused={focused}
                    setFocused={setFocused}
                    changeBlock={changeBlock}
                    deleteBlock={deleteBlock}
                    duplicateBlock={duplicateBlock}
                />
            ))}
        </div>
    );
});

export function FormBlocksEditor(props) {
    // Metadata
    const [title, setTitle] = useState(props.data.title);
    const [description, setDescription] = useState(props.data.description);
    const [proposeNewsletter, setProposeNewsletter] = useState(props.data.proposeNewsletter);
    const [onlyForMembers, setOnlyForMembers] = useState(props.data.onlyForMembers);
    const [redirectUrl, setRedirectUrl] = useState(props.data.redirectUrl);

    // Blocks
    const [focused, setFocused] = useState(0);
    const [blocks, setBlocks] = useState(props.data.blocks);

    // Save helper
    function save(title, description, proposeNewsletter, onlyForMembers, redirectUrl, blocks) {
        if (saveTimeout) {
            clearTimeout(saveTimeout);
        }

        saveTimeout = setTimeout(() => {
            props.refreshStatus('saving');

            httpClient
                .post(
                    props.updateUrl,
                    JSON.stringify({
                        title: title,
                        description: description,
                        proposeNewsletter: proposeNewsletter,
                        onlyForMembers: onlyForMembers,
                        redirectUrl: redirectUrl,
                        blocks: blocks,
                    }),
                    { headers: { 'Content-Type': 'text/json' } }
                )
                .then(() => props.refreshStatus('saved'))
                .catch(() => props.refreshStatus('error'));
        }, 700);
    }

    // State update helpers
    const addBlock = (type) => {
        const newBlocks = [];
        for (let i in blocks) {
            newBlocks.push(blocks[i]);
        }

        const id = 'created-' + Math.floor(Math.random() * 9999999);
        newBlocks.push({ ...blockBuilder.createEmptyData(type), id: id });

        setBlocks(newBlocks);
        setTimeout(() => setFocused(id), 10);
        save(title, description, proposeNewsletter, onlyForMembers, redirectUrl, newBlocks);
    };

    const duplicateBlock = (block) => {
        const newBlocks = [];

        for (let i in blocks) {
            newBlocks.push(blocks[i]);

            if (blocks.hasOwnProperty(i) && blocks[i].id === block.id) {
                newBlocks.push({ ...blocks[i], id: 'created-' + Math.floor(Math.random() * 9999999) });
            }
        }

        setBlocks(newBlocks);
        save(title, description, proposeNewsletter, onlyForMembers, redirectUrl, newBlocks);
    };

    const changeBlock = (oldBlock, newBlock) => {
        const newBlocks = [];
        for (let i in blocks) {
            if (blocks.hasOwnProperty(i) && blocks[i].id === oldBlock.id) {
                newBlocks.push(newBlock);
            } else {
                newBlocks.push(blocks[i]);
            }
        }

        setBlocks(newBlocks);
        save(title, description, proposeNewsletter, onlyForMembers, redirectUrl, newBlocks);
    };

    const deleteBlock = (block) => {
        const newBlocks = [];
        for (let i in blocks) {
            if (blocks.hasOwnProperty(i) && blocks[i].id !== block.id) {
                newBlocks.push(blocks[i]);
            }
        }

        setBlocks(newBlocks);
        save(title, description, proposeNewsletter, onlyForMembers, redirectUrl, newBlocks);
    };

    return (
        <div className="form-blocks p-4">
            <div
                className={'form-block mb-3 ' + (focused === 0 ? 'form-block-focused' : '')}
                onClick={() => setFocused(0)}
            >
                <MetadataView
                    focused={focused === 0}
                    title={title}
                    setTitle={(newTitle) => {
                        setTitle(newTitle);
                        save(newTitle, description, proposeNewsletter, onlyForMembers, redirectUrl, blocks);
                    }}
                    description={description}
                    setDescription={(newDescription) => {
                        setDescription(newDescription);
                        save(title, newDescription, proposeNewsletter, onlyForMembers, redirectUrl, blocks);
                    }}
                    proposeNewsletter={proposeNewsletter}
                    setProposeNewsletter={(newProposeNewsletter) => {
                        setProposeNewsletter(newProposeNewsletter);
                        save(title, description, newProposeNewsletter, onlyForMembers, redirectUrl, blocks);
                    }}
                    onlyForMembers={onlyForMembers}
                    setOnlyForMembers={(newOnlyForMembers) => {
                        setOnlyForMembers(newOnlyForMembers);
                        save(title, description, proposeNewsletter, newOnlyForMembers, redirectUrl, blocks);
                    }}
                    redirectUrl={redirectUrl}
                    setRedirectUrl={(newRedirectUrl) => {
                        setRedirectUrl(newRedirectUrl);
                        save(title, description, proposeNewsletter, onlyForMembers, newRedirectUrl, blocks);
                    }}
                />
            </div>

            <SortableList
                items={blocks}
                useDragHandle={true}
                focused={focused}
                setFocused={setFocused}
                changeBlock={changeBlock}
                deleteBlock={deleteBlock}
                duplicateBlock={duplicateBlock}
                onSortEnd={({ oldIndex, newIndex }) => {
                    const newBlocks = arrayMoveImmutable(blocks, oldIndex, newIndex);
                    setBlocks(newBlocks);
                    save(title, description, proposeNewsletter, onlyForMembers, redirectUrl, newBlocks);
                }}
            />

            <div className="mt-5">
                <AddBlockButton onAdd={(type) => addBlock(type)} />
            </div>
        </div>
    );
}
