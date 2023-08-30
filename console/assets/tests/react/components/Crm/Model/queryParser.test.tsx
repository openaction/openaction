import { parseQueryToSearchFilters } from '../../../../../react/components/Crm/Model/queryParser';

describe('parseQueryToSearchFilters', () => {
    const cases = [
        {
            query: 'dateNaissance > "2023-04-12"',
            expectedFilter: 'profile_birthdate_int  > 20230412  ',
        },
        {
            query: 'dateNaissance > "2023-04-12" et prenom = "Titouan"',
            expectedFilter: "profile_birthdate_int  > 20230412  AND profile_first_name  = 'Titouan'  ",
        },
        {
            query: '(dateNaissance > "2023-04-12" et nom = "Galopin") ou prenom = "Titouan"',
            expectedFilter:
                "(profile_birthdate_int  > 20230412  AND profile_last_name  = 'Galopin'  ) OR profile_first_name  = 'Titouan'  ",
        },
        {
            query: 'tags != "Actif" et newsletters = true',
            expectedFilter: "tags_names  != 'Actif'  AND settings_receive_newsletters  = true  ",
        },
    ];

    for (let i in cases) {
        it(cases[i].query, async () => {
            expect(parseQueryToSearchFilters(cases[i].query, '2023-04-12')).toEqual(cases[i].expectedFilter);
        });
    }
});
