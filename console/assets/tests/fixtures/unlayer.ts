import { Design, MergeTag, Unlayer } from '../../react/bridge/unlayer';

class UnlayerMock implements Unlayer {
    public callbacks: { [key: string]: any } = {};
    public design: Design | null = null;
    public saved: boolean = false;
    public mergeTags: MergeTag[] = [];
    public bodyValues: object = {};

    registerCallback(type, callback): void {
        this.callbacks[type] = callback;
    }

    addEventListener(type: string, callback): void {
        this.callbacks[type] = callback;
    }

    loadDesign(design): void {
        this.design = design;
    }

    saveDesign(callback): void {
        this.saved = true;
        callback(this.design);
    }

    exportHtml(callback): void {
        callback({
            design: this.design,
            html: '<html></html>',
            chunks: {
                body: 'body',
                css: 'css',
                js: 'js',
                fonts: [],
            },
        });
    }

    setMergeTags(mergeTags): void {
        this.mergeTags = mergeTags;
    }

    setBodyValues(bodyValue): void {
        this.bodyValues = bodyValue;
    }
}

export function mockUnlayerEditor(): Unlayer {
    return new UnlayerMock();
}
