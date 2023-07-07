import React from 'react';
import { render } from 'react-dom';
import Joyride from 'react-joyride';
import { Controller } from 'stimulus';

export default class extends Controller {
    connect() {
        if (localStorage.getItem('onboarding_joyride_project')) {
            return;
        }

        localStorage.setItem('onboarding_joyride_project', '1');

        let steps = [
            {
                content: (
                    <>
                        <div className="mb-3">
                            Les projets Citipo permettent aux membres de votre équipe de travailler sur des zones
                            géographiques, des thématiques ou des outils adaptés à leurs rôles.
                        </div>
                    </>
                ),
                target: 'body',
                placement: 'center',
            },
            {
                content: (
                    <>
                        <div className="mb-3">
                            La barre latérale à gauche vous donne accès aux différentes fonctionnalités de Citipo : la
                            modification du site internet, l'envoi de campagnes emails ou SMS, la commande d'imprimés,
                            ...
                        </div>

                        <div>
                            Certains modules ne sont cependant parfois pas présents, soit parce qu'ils ont été
                            désactivés par votre administrateur, soit parce qu'il ne sont pas inclus dans le plan que
                            vous avez choisi.
                        </div>
                    </>
                ),
                target: '.world-sidebar',
                placement: 'right',
            },
        ];

        if (document.querySelector('.world-sidebar-item-appearance')) {
            steps.push({
                content: (
                    <div>Vous pouvez par exemple modifier l'apparence de votre site internet en cliquant ici ...</div>
                ),
                target: '.world-sidebar-item-appearance',
                placement: 'right',
            });

            steps.push({
                content: (
                    <div>... ou encore modifier le contenu de votre site internet en utilisant les modules dédiés.</div>
                ),
                target: '.world-sidebar-category-website',
                placement: 'right',
            });
        }

        if (document.querySelector('.world-sidebar-category-community')) {
            steps.push({
                content: (
                    <>
                        <div className="mb-3">
                            La section Communauté vous permet d'accéder à la liste de vos contacts (toutes les personnes
                            ayant interagi avec votre organisation).
                        </div>

                        <div>
                            Cette section vous permet aussi d'envoyer des campagnes emails ou SMS à ces contacts, à
                            organiser des campagnes d'appels, à commander des imprimés, ...
                        </div>
                    </>
                ),
                target: '.world-sidebar-category-community',
                placement: 'right',
            });
        }

        if (document.querySelector('.world-sidebar-category-socials')) {
            steps.push({
                content: (
                    <div>
                        La section Réseaux sociaux vous permet de connecter différents réseaux sociaux à votre site,
                        aussi bien pour permettre à vos visiteurs de trouver vos comptes sociaux que pour leur permettre
                        de partager vos contenus sur leurs réseaux.
                    </div>
                ),
                target: '.world-sidebar-category-socials',
                placement: 'right',
            });
        }

        steps.push({
            content: <div>À tout moment, vous pouvez lire notre documentation pour apprendre à utiliser Citipo.</div>,
            target: '.world-user-item-documentation',
            placement: 'bottom',
        });

        steps.push({
            content: (
                <div>
                    Enfin, pour toute question, n'hésitez pas à nous contacter : nous serons ravis de vous aider dès que
                    vous en avez besoin !
                </div>
            ),
            target: '.world-user-item-support',
            placement: 'bottom',
        });

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
