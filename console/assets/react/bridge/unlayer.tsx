declare const unlayer: any;

export function createEditor(options: Options): Unlayer {
    return unlayer.createEditor(options);
}

interface AppearanceConfig {
    readonly theme?: 'light' | 'dark';
    readonly panels?: {
        readonly tools?: {
            readonly dock: 'right' | 'left';
        };
    };
}

interface User {
    readonly id?: number;
    readonly name?: string;
    readonly email?: string;
}

interface GroupedMergeTag {
    readonly name: string;
    readonly mergeTags: Array<SimpleMergeTag | GroupedMergeTag>;
}

interface SimpleMergeTag {
    readonly name: string;
    readonly value: string;
    readonly sample?: string;
}

interface ConditionalMergeTagRule {
    readonly name: string;
    readonly before: string;
    readonly after: string;
}

interface ConditionalMergeTag {
    readonly name: string;
    readonly rules: ConditionalMergeTagRule[];
    readonly mergeTags?: SimpleMergeTag[];
}

export type MergeTag = SimpleMergeTag | ConditionalMergeTag | GroupedMergeTag;

interface GroupedSpecialLink {
    readonly name: string;
    readonly specialLinks: Array<SimpleSpecialLink | GroupedSpecialLink>;
}

interface SimpleSpecialLink {
    readonly name: string;
    readonly href: string;
    readonly target: string;
}

export type SpecialLink = SimpleSpecialLink | GroupedSpecialLink;

interface DesignTagConfig {
    readonly delimiter: [string, string];
}

interface ToolConfig {
    readonly enabled?: boolean;
    readonly position?: number;
    readonly properties?: object;
}

interface ToolsConfig {
    readonly [key: string]: ToolConfig;
}

interface EditorConfig {
    readonly minRows?: number;
    readonly maxRows?: number;
}

interface Features {
    readonly preview?: boolean;
    readonly imageEditor?: boolean;
    readonly undoRedo?: boolean;
    readonly preheaderText?: boolean;
    readonly stockImages?: boolean;
    readonly textEditor?: TextEditor;
}

interface TextEditor {
    readonly spellChecker?: boolean;
    readonly tables?: boolean;
    readonly cleanPaste?: boolean;
    readonly emojis?: boolean;
}

type Translations = Record<string, Record<string, string>>;

interface DisplayCondition {
    readonly type: string;
    readonly label: string;
    readonly description: string;
    readonly before: string;
    readonly after: string;
}

interface CustomFont {
    readonly label: string;
    readonly value: string;
    readonly url?: string;
}

interface FontsConfig {
    readonly showDefaultFonts?: boolean;
    readonly customFonts?: CustomFont[];
}

export interface Options {
    readonly id?: string;
    readonly displayMode?: 'email' | 'web';
    readonly projectId?: number;
    readonly locale?: string;
    readonly appearance?: AppearanceConfig;
    readonly fonts?: FontsConfig;
    readonly user?: User;
    readonly mergeTags?: MergeTag[];
    readonly specialLinks?: SpecialLink[];
    readonly designTags?: { [key: string]: string };
    readonly designTagsConfig?: DesignTagConfig;
    readonly tools?: ToolsConfig;
    readonly blocks?: object[];
    readonly editor?: EditorConfig;
    readonly safeHtml?: boolean;
    readonly customJS?: string[];
    readonly customCSS?: string[];
    readonly features?: Features;
    readonly translations?: Translations;
    readonly displayConditions?: DisplayCondition[];
}

export interface Design {
    readonly counters?: object;
    readonly body: {
        readonly rows: object[];
        readonly values?: object;
    };
}

interface HtmlExport {
    readonly design: Design;
    readonly html: string;
    readonly chunks: {
        body: string;
        css: string;
        js: string;
        fonts: object[];
    };
}

interface FileInfo {
    readonly accepted: File[];
    readonly attachments: File[];
}

export interface FileUploadDoneData {
    readonly progress: number;
    readonly url?: string;
}

type SaveDesignCallback = (data: Design) => void;
type ExportHtmlCallback = (data: HtmlExport) => void;
type EventCallback = (data: object) => void;
type FileUploadCallback = (file: FileInfo, done: FileUploadDoneCallback) => void;
type FileUploadDoneCallback = (data: FileUploadDoneData) => void;
type DisplayConditionDoneCallback = (data: DisplayCondition | null) => void;
type DisplayConditionCallback = (data: DisplayCondition | object, done: DisplayConditionDoneCallback) => void;

export interface Unlayer {
    registerCallback(type: 'image', callback: FileUploadCallback): void;
    registerCallback(type: 'displayCondition', callback: DisplayConditionCallback): void;
    addEventListener(type: string, callback: EventCallback): void;
    loadDesign(design: Design): void;
    saveDesign(callback: SaveDesignCallback): void;
    exportHtml(callback: ExportHtmlCallback): void;
    setMergeTags(mergeTags: ReadonlyArray<MergeTag>): void;
    setBodyValues(bodyValue: object): void;
}
