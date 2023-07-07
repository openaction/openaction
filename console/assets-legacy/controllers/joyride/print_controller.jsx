import React from 'react';
import { render } from 'react-dom';
import Joyride from 'react-joyride';
import { Controller } from 'stimulus';

export default class extends Controller {
    connect() {
        if (localStorage.getItem('onboarding_joyride_print')) {
            return;
        }

        localStorage.setItem('onboarding_joyride_print', '1');

        let steps = [
            {
                content: (
                    <div>Le projet que vous venez d'ouvrir est un projet focalisé sur la commande d'imprimés.</div>
                ),
                target: 'body',
                placement: 'center',
            },
            {
                content: (
                    <div>
                        Au sein d'un tel projet, vous retrouverez ici votre panier (les commandes que vous n'avez pas
                        encore effectuées) ...
                    </div>
                ),
                target: '.printing-block-drafts',
                placement: 'bottom',
            },
            {
                content: (
                    <div>
                        ... et vous retrouverez ici les commandes en cours d'impression, en cours de livraison et
                        livrées.
                    </div>
                ),
                target: '.printing-block-ordered',
                placement: 'top',
            },
            {
                content: <div>Pour démarrer, vous pouvez commencer par créer une première commande.</div>,
                target: '.printing-create-button',
                placement: 'bottom',
            },
            {
                content: (
                    <div>À tout moment, vous pouvez lire notre documentation pour apprendre à utiliser Citipo.</div>
                ),
                target: '.world-user-item-documentation',
                placement: 'bottom',
            },
            {
                content: (
                    <div>
                        Enfin, pour toute question, n'hésitez pas à nous contacter : nous serons ravis de vous aider dès
                        que vous en avez besoin !
                    </div>
                ),
                target: '.world-user-item-support',
                placement: 'bottom',
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
