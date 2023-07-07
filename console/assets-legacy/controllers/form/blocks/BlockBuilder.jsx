import { HeaderBlockHandler } from './handler/HeaderBlockHandler';
import { HtmlBlockHandler } from './handler/HtmlBlockHandler';
import { ParagraphBlockHandler } from './handler/ParagraphBlockHandler';
import { CheckboxBlockHandler } from './handler/CheckboxBlockHandler';
import { TextareaBlockHandler } from './handler/TextareaBlockHandler';
import { TextBlockHandler } from './handler/TextBlockHandler';
import { EmailBlockHandler } from './handler/EmailBlockHandler';
import { RadioBlockHandler } from './handler/RadioBlockHandler';
import { RatingBlockHandler } from './handler/RatingBlockHandler';
import { SelectBlockHandler } from './handler/SelectBlockHandler';
import { BirthdateBlockHandler } from './handler/BirthdateBlockHandler';
import { DateBlockHandler } from './handler/DateBlockHandler';
import { TimeBlockHandler } from './handler/TimeBlockHandler';
import { PictureBlockHandler } from './handler/PictureBlockHandler';
import { JobTitleBlockHandler } from './handler/JobTitleBlockHandler';
import { CompanyBlockHandler } from './handler/CompanyBlockHandler';
import { PhoneBlockHandler } from './handler/PhoneBlockHandler';
import { CountryBlockHandler } from './handler/CountryBlockHandler';
import { ZipCodeBlockHandler } from './handler/ZipCodeBlockHandler';
import { LastNameBlockHandler } from './handler/LastNameBlockHandler';
import { FirstNameBlockHandler } from './handler/FirstNameBlockHandler';
import { TagRadioBlockHandler } from './handler/TagRadioBlockHandler';
import { TagCheckboxBlockHandler } from './handler/TagCheckboxBlockHandler';
import { TagHiddenBlockHandler } from './handler/TagHiddenBlockHandler';
import { NationalityBlockHandler } from './handler/NationalityBlockHandler';
import { FormalTitleBlockHandler } from './handler/FormalTitleBlockHandler';
import { StreetAddressBlockHandler } from './handler/StreetAddressBlockHandler';
import { CityBlockHandler } from './handler/CityBlockHandler';
import { MiddleNameBlockHandler } from './handler/MiddleNameBlockHandler';
import { GenderBlockHandler } from './handler/GenderBlockHandler';
import { WorkPhoneBlockHandler } from './handler/WorkPhoneBlockHandler';
import { SocialFacebookBlockHandler } from './handler/SocialFacebookBlockHandler';
import { SocialTwitterBlockHandler } from './handler/SocialTwitterBlockHandler';
import { SocialLinkedInBlockHandler } from './handler/SocialLinkedInBlockHandler';
import { SocialTelegramBlockHandler } from './handler/SocialTelegramBlockHandler';
import { SocialWhatsappBlockHandler } from './handler/SocialWhatsappBlockHandler';
import { StreetAddress2BlockHandler } from './handler/StreetAddress2BlockHandler';
import { FileBlockHandler } from './handler/FileBlockHandler';

class BlockBuilder {
    constructor() {
        this.blockHandlers = {
            // Normal fields
            text: new TextBlockHandler(),
            textarea: new TextareaBlockHandler(),
            select: new SelectBlockHandler(),
            radio: new RadioBlockHandler(),
            rating: new RatingBlockHandler(),
            checkbox: new CheckboxBlockHandler(),
            date: new DateBlockHandler(),
            time: new TimeBlockHandler(),
            file: new FileBlockHandler(),

            // Automatic fields
            email: new EmailBlockHandler(),
            formal_title: new FormalTitleBlockHandler(),
            firstname: new FirstNameBlockHandler(),
            middlename: new MiddleNameBlockHandler(),
            lastname: new LastNameBlockHandler(),
            birthdate: new BirthdateBlockHandler(),
            gender: new GenderBlockHandler(),
            nationality: new NationalityBlockHandler(),
            company: new CompanyBlockHandler(),
            job_title: new JobTitleBlockHandler(),
            phone: new PhoneBlockHandler(),
            work_phone: new WorkPhoneBlockHandler(),
            social_facebook: new SocialFacebookBlockHandler(),
            social_twitter: new SocialTwitterBlockHandler(),
            social_linkedin: new SocialLinkedInBlockHandler(),
            social_telegram: new SocialTelegramBlockHandler(),
            social_whatsapp: new SocialWhatsappBlockHandler(),
            street_address: new StreetAddressBlockHandler(),
            street_address_2: new StreetAddress2BlockHandler(),
            city: new CityBlockHandler(),
            zip_code: new ZipCodeBlockHandler(),
            country: new CountryBlockHandler(),
            picture: new PictureBlockHandler(),

            // Tag fields
            tag_radio: new TagRadioBlockHandler(),
            tag_checkbox: new TagCheckboxBlockHandler(),
            tag_hidden: new TagHiddenBlockHandler(),

            // Custom content
            header: new HeaderBlockHandler(),
            paragraph: new ParagraphBlockHandler(),
            html: new HtmlBlockHandler(),
        };
    }

    createEmptyData(type) {
        return {
            type: type,
            required: false,
            ...this.blockHandlers[type].createEmptyData(),
        };
    }

    createDefaultView(block) {
        if (typeof this.blockHandlers[block.type] === 'undefined') {
            throw new Error('Invalid block type ' + block.type);
        }

        return this.blockHandlers[block.type].createDefaultView(block);
    }

    createFocusedView(block, onChange) {
        if (typeof this.blockHandlers[block.type] === 'undefined') {
            throw new Error('Invalid block type ' + block.type);
        }

        return this.blockHandlers[block.type].createFocusedView(block, onChange);
    }
}

export const blockBuilder = new BlockBuilder();
