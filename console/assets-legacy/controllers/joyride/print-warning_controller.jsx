import React from 'react';
import { render } from 'react-dom';
import Joyride from 'react-joyride';
import { Controller } from 'stimulus';

export default class extends Controller {
    connect() {
        if (localStorage.getItem('onboarding_joyride_print_warning')) {
            // return;
        }

        localStorage.setItem('onboarding_joyride_print_warning', '1');

        let steps = [
            {
                content: (
                    <div className="text-left">
                        <h4>
                            <span className="text-danger">Important : </span>
                            Impression de la propagande officielle du deuxième tour
                        </h4>

                        <p>
                            Vous pouvez déposer vos fichiers de propagande officielle pour le second tour des élections.
                        </p>

                        <p>
                            Pour les Français résidents hors de France : dépôt des fichiers impératif avant le lundi 6
                            Juin à 16h.
                        </p>

                        <p>
                            Pour toutes les autres circonscriptions : dépôt des fichiers impératif avant le samedi 11
                            juin à 12h.
                        </p>

                        <p>
                            Compte tenu des délais d'impressions pour le second tour, aucun document ne sera accepté au
                            delà de ces limites.
                        </p>
                    </div>
                ),
                target: 'body',
                placement: 'center',
            },
        ];

        render(
            <Joyride
                continuous={true}
                showSkipButton={true}
                showProgress={true}
                steps={steps}
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
