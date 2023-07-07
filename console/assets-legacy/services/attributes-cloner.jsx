/*
 * Clone the attributes of a DOM node to reuse them elsewhere,
 * for instance in a React component.
 */
class AttributesCloner {
    cloneAttributesForReact(node, ignoredAttributes = {}) {
        return this.cloneAttributes(node, ignoredAttributes, {
            class: 'className',
            value: 'defaultValue',
        });
    }

    cloneAttributes(node, ignoredAttributes = {}, renames = {}) {
        const attrNames = node.getAttributeNames();

        let attrs = {};
        for (let i in attrNames) {
            let name = attrNames[i];

            if (ignoredAttributes.indexOf(name) > -1) {
                continue;
            }

            if (typeof renames[name] !== 'undefined') {
                name = renames[name];
            }

            attrs[name] = node.getAttribute(attrNames[i]);
        }

        return attrs;
    }
}

export const attributesCloner = new AttributesCloner();
