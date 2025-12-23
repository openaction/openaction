export interface Project {
    _links: {
        self: string;
        posts: string;
        pages: string;
        events: string;
        stylesheet: string;
        javascript: string;
        javascript_custom: string;
        analytics: string;
    };
    uuid: string;
    id: string;
    name: string;
    locale: string;
    domain: string;
    logoDark: string | null;
    logoWhite: string | null;
    icon: string | null;
    favicon: string | null;
    sharer: string | null;
    primary: string;
    secondary: string;
    third: string;
    fontTitle: string;
    fontText: string;
    metaTitle: string;
    metaDescription: string;
    mainImage: string | null;
    mainVideo: string | null;
    introPosition: string;
    introOverlay: boolean;
    introTitle: string | null;
    introContent: string | null;
    animateElements: boolean;
    animateLinks: boolean;
    terminology: {
        posts: string;
        events: string;
        trombinoscope: string;
        manifesto: string;
        newsletter: string;
        acceptPrivacy: string;
        socialNetworks: string;
        membershipLogin: string;
        membershipRegister: string;
        membershipArea: string;
    };
    theme: Record<string, string>;
    theme_assets: Record<string, string>;
    project_assets: Record<string, string>;
    redirections: Array<{
        source: string;
        target: string;
        code: number;
    }>;
    importedRedirections: Array<{
        source: string;
        target: string;
        code: number;
    }>;
    tools: string[];
    access: {
        username: string | null;
        password: string | null;
    };
    socials: {
        email: string | null;
        phone: string | null;
        facebook: string | null;
        twitter: string | null;
        instagram: string | null;
        linkedin: string | null;
        youtube: string | null;
        medium: string | null;
        telegram: string | null;
        snapchat: string | null;
        whatsapp: string | null;
        tiktok: string | null;
        threads: string | null;
        bluesky: string | null;
        mastodon: string | null;
    };
    socialSharers: {
        facebook: boolean;
        twitter: boolean;
        bluesky: boolean;
        linkedin: boolean;
        telegram: boolean;
        whatsapp: boolean;
        email: boolean;
    };
    legal: {
        name: string;
        email: string;
        address: string;
        publisherName: string;
        publisherRole: string;
    };
    membership: Record<string, string>;
    membershipMainPage: string;
    captchaSiteKey: string | null;
    captchaSecretKey: string | null;
    enableGdprFields: boolean;
}
