export function mockTagsApiResponse(): Promise<{ data: any }> {
    return new Promise((resolve) =>
        resolve({
            data: {
                5: { name: 'ContainsTagInside', slug: 'containstaginside' },
                6: { name: 'contains tag keyword lowercase', slug: 'contains-tag-keyword-lowercase' },
                4: { name: 'DontStartWithTag', slug: 'dontstartwithtag' },
                1: { name: 'ExampleTag', slug: 'exampletag' },
                3: { name: 'StartWithTag', slug: 'startwithtag' },
                2: { name: 'Tag', slug: 'tag' },
                7: { name: 'tag start with keyword lowercase', slug: 'tag-start-with-keyword-lowercase' },
            },
        })
    );
}
