import React, { useEffect } from 'react';
import { Design, Options, Unlayer, FileUploadDoneData, MergeTag, createEditor } from '../../bridge/unlayer';

export * from '../../bridge/unlayer';

let lastEditorId = 1;

export interface Appearance {
    fontTitle: string;
    fontText: string;
    colorPrimary: string;
    colorSecondary: string;
    colorThird: string;
}

export interface Labels {
    defaultTitle: string;
    defaultText: string;
}

export type Fonts = { [key: string]: string };

interface Props {
    displayMode: 'email' | 'web';
    projectId: number | null;
    appearance: Appearance;
    labels: Labels;
    fonts?: Fonts;
    design: Design;
    mergeTags?: MergeTag[];
    onUpload: (editor: Unlayer, file: File, done: (data: FileUploadDoneData) => void) => void;
    onSave: (editor: Unlayer) => void;
}

export function Editor(props: Props) {
    // Load editor on mount
    useEffect(() => {
        const customFonts = !props.fonts
            ? []
            : Object.keys(props.fonts).map((family) => {
                  return {
                      label: family,
                      value: "'" + family + "', sans-serif",
                      url: props.fonts[family],
                  };
              });

        const options: Options = Object.assign(
            {},
            {
                id: 'unlayer-editor-' + lastEditorId,
                displayMode: props.displayMode,
                projectId: props.projectId,
                locale: 'fr' === window.Citipo.locale ? 'fr-FR' : 'en-US',
                mergeTags: props.mergeTags || [],
                fonts: {
                    showDefaultFonts: props.displayMode === 'email',
                    customFonts: props.displayMode === 'email' ? undefined : customFonts,
                },
                translations: {
                    fr: {
                        'content_tools.columns': 'Colonnes',
                        'labels.merge_tags': 'Données dynamiques',
                    },
                },
                features: {
                    preheaderText: false,
                    stockImages: true,
                },
                tools: {
                    text: {
                        position: 1,
                        properties: {
                            text: {
                                value: props.labels.defaultText,
                            },
                        },
                    },
                    image: {
                        position: 2,
                    },
                    heading: {
                        position: 3,
                        properties: {
                            text: {
                                value: props.labels.defaultTitle,
                            },
                        },
                    },
                    button: {
                        position: 4,
                        properties: {
                            buttonColors: {
                                value: {
                                    color: '#FFFFFF',
                                    backgroundColor: props.appearance.colorSecondary
                                        ? '#' + props.appearance.colorSecondary
                                        : null,
                                    hoverColor: '#FFFFFF',
                                    hoverBackgroundColor: props.appearance.colorSecondary
                                        ? '#' + props.appearance.colorSecondary
                                        : null,
                                },
                            },
                        },
                    },
                    columns: {
                        position: 5,
                    },
                    video: {
                        position: 6,
                    },
                    menu: {
                        position: 7,
                    },
                    divider: {
                        position: 8,
                    },
                    html: {
                        position: 9,
                    },
                    form: {
                        enabled: false,
                    },
                },
            }
        );

        const editor = createEditor(options);

        editor.addEventListener('editor:ready', () => {
            editor.loadDesign(props.design);
            editor.setBodyValues({
                backgroundColor: '#ffffff',
                contentWidth: 'web' === props.displayMode ? '940px' : '500px',
                fontFamily: {
                    label: 'Open Sans',
                    value: "'Open Sans', OpenSans,-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica Neue,Ubuntu,sans-serif",
                },
                linkStyle: {
                    linkColor: props.appearance.colorThird ? '#' + props.appearance.colorThird : null,
                    linkHoverColor: "props.appearance.colorThird ? '#' + props.appearance.colorThird : null",
                    linkUnderline: false,
                    linkHoverUnderline: true,
                },
            });

            // Register change handler
            editor.addEventListener('design:updated', () => {
                props.onSave(editor);
            });

            // Register upload handler
            editor.registerCallback('image', (file, done) => {
                props.onUpload(editor, file.attachments[0], (data) => {
                    done(data);
                });
            });

            // Initial save
            props.onSave(editor);
        });

        ++lastEditorId;
    }, []);

    return <div id={'unlayer-editor-' + lastEditorId} />;
}
