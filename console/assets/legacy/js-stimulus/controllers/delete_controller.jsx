import React, { useState } from 'react';
import { render } from 'react-dom';
import { Controller } from 'stimulus';
import { Modal } from '../components/Modal';
import { translator } from '../services/translator';
import { httpClient } from '../services/http-client';

function ConfirmLink(props) {
    const [modalOpened, setModalOpened] = useState(false);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(false);

    const closeModal = () => {
        if (!loading) {
            setModalOpened(false);
        }
    };

    const handleLinkClick = (e) => {
        setModalOpened(true);
        e.preventDefault();

        return false;
    };

    const handleYesClick = () => {
        setError(false);
        setLoading(true);

        httpClient
            .get(props.url, { headers: { 'X-Ajax-Confirm': '1' } })
            .then((response) => {
                if (response.data.hasOwnProperty('redirect') && response.data.redirect !== false) {
                    location.href = response.data.redirect;
                } else {
                    props.onConfirmed();
                    setModalOpened(false);
                    setLoading(false);
                }
            })
            .catch(() => {
                setError(true);
                setLoading(false);
            });
    };

    return (
        <span>
            <a
                href=""
                className={props.className}
                onClick={handleLinkClick}
                dangerouslySetInnerHTML={{ __html: props.content }}
            />

            <Modal
                opened={modalOpened}
                title={translator.trans('delete_confirm.title')}
                onClose={closeModal}
                footer={
                    <div className="modal-footer text-right">
                        <button
                            type="button"
                            className="btn btn-secondary mr-2"
                            disabled={loading}
                            onClick={closeModal}
                        >
                            {translator.trans('delete_confirm.no')}
                        </button>

                        <button type="button" className="btn btn-danger" disabled={loading} onClick={handleYesClick}>
                            {props.deleteLabel ? props.deleteLabel : translator.trans('delete_confirm.yes')}
                        </button>
                    </div>
                }
            >
                <div
                    className="d-flex justify-content-center align-items-center text-center px-4"
                    style={{ height: '100px' }}
                >
                    <div>
                        {error ? (
                            <span className="text-danger">{translator.trans('delete_confirm.error')}</span>
                        ) : loading ? (
                            <span className="h3 mb-0">
                                <i className="fal fa-circle-notch fa-spin" />
                            </span>
                        ) : (
                            <span>{props.confirmMessage}</span>
                        )}
                    </div>
                </div>
            </Modal>
        </span>
    );
}

export default class extends Controller {
    connect() {
        // Create a wrapper
        const wrapper = document.createElement('span');
        this.element.parentNode.insertBefore(wrapper, this.element);
        wrapper.appendChild(this.element);

        const target = this.element.getAttribute('data-target');
        const targetUrl = this.element.getAttribute('data-target-url');

        render(
            <ConfirmLink
                url={this.element.getAttribute('href')}
                confirmMessage={this.element.getAttribute('data-message')}
                successMessage={this.element.getAttribute('data-success')}
                deleteLabel={this.element.getAttribute('data-delete-label')}
                className={this.element.getAttribute('class')}
                content={this.element.innerHTML}
                onConfirmed={() => {
                    if (target) {
                        document.querySelectorAll(target).forEach((el) => {
                            el.parentNode.removeChild(el);
                        });
                        document.querySelectorAll('.alert-bg-success').forEach((el) => {
                            el.parentNode.removeChild(el);
                        });
                    }

                    if (targetUrl) {
                        location.href = targetUrl;
                    }
                }}
            />,
            wrapper
        );
    }
}
