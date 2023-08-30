import React from 'react';
import { render } from 'react-dom';
import Joyride from 'react-joyride';
import { Controller } from 'stimulus';

export default class extends Controller {
    connect() {
        if (localStorage.getItem('onboarding_joyride_organization')) {
            return;
        }

        localStorage.setItem('onboarding_joyride_organization', '1');

        render(
            <Joyride
                continuous={true}
                showSkipButton={true}
                showProgress={true}
                steps={[
                    {
                        content: (
                            <>
                                <h3 className="mb-3">Bienvenue !</h3>

                                <div>Si vous le voulez bien, explorons ensemble comment fonctionne la plateforme !</div>
                            </>
                        ),
                        target: 'body',
                        placement: 'center',
                    },
                    {
                        content: (
                            <>
                                <div className="mb-3">
                                    Cette interface d'administration fonctionne grâce à un système de projets : un
                                    projet regroupe différentes fonctionnalités au même endroit, comme par exemple un
                                    site internet, des campagnes emails, des commandes d'imprimés, ...
                                </div>

                                <div>Pour continuer, cliquez sur un projet pour l'ouvrir.</div>
                            </>
                        ),
                        target: '.projects-item',
                    },
                ]}
                locale={{
                    back: 'Retour',
                    close: 'Fermer',
                    last: 'Fermer',
                    next: 'Suivant',
                    open: 'Ouvrir',
                    skip: 'Ignorer',
                }}
            />,
            this.element
        );
    }
}
