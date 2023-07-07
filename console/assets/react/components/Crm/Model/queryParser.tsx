import jsep, { BinaryExpression, Compound, Expression, Identifier, Literal } from 'jsep';

jsep.addBinaryOp('=', 10);

export function isQueryValid(query: string): boolean {
    try {
        visitNode(jsep(query));

        return true;
    } catch (e) {
        return false;
    }
}

export function parseQueryToSearchFilters(query: string, currentDate: string | null = null): string {
    return visitNode(jsep(query));
}

function visitNode(node: Expression): string {
    let searchFilter = '';

    switch (node.type) {
        case 'Compound':
            for (let i in (node as Compound).body) {
                searchFilter += visitNode((node as Compound).body[i]);
            }

            break;

        case 'BinaryExpression':
            searchFilter += visitNode((node as BinaryExpression).left) + ' ';
            searchFilter += normalizeOperator((node as BinaryExpression).operator) + ' ';
            searchFilter += visitNode((node as BinaryExpression).right) + ' ';

            break;

        case 'SequenceExpression':
            searchFilter += '(';
            for (let i in (node as any).expressions) {
                searchFilter += visitNode((node as any).expressions[i]);
            }
            searchFilter += ') ';

            break;

        case 'Identifier':
            searchFilter += normalizeIdentifier((node as Identifier).name.toLowerCase()) + ' ';

            break;

        case 'Literal':
            let value = (node as Literal).value;

            if (typeof value === 'string') {
                if (value.match(/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/)) {
                    // Date
                    searchFilter += parseInt(value.replace(/-/g, '')) + ' ';
                } else {
                    // Other fields
                    searchFilter += "'" + value.replace(/'/, "'") + "' ";
                }
            } else if (typeof value === 'boolean') {
                searchFilter += value ? 'true ' : 'false ';
            } else if (typeof value === 'number') {
                searchFilter += value + ' ';
            }

            break;
    }

    return searchFilter;
}

function normalizeOperator(operator: string): string {
    const operators = {
        '==': '=',
        '=': '=',
        '!=': '!=',
        '>': '>',
        '>=': '>=',
        '<': '<',
        '<=': '<=',
    };

    if (typeof operators[operator] === 'undefined') {
        throw new Error('Invalid operator ' + operator);
    }

    return operators[operator];
}

function normalizeIdentifier(identifier: string): string {
    const identifiers = {
        // Operators
        et: 'AND',
        and: 'AND',
        ou: 'OR',
        or: 'OR',

        // Fields
        prenom: 'profile_first_name',
        nom: 'profile_last_name',
        datenaissance: 'profile_birthdate_int',
        age: 'profile_age',
        pays: 'area_country_code',
        region: 'area_province_name',
        departement: 'area_district_name',
        codepostal: 'area_zip_code_name',
        tags: 'tags_names',
        projects: 'projects_names',
        newsletters: 'settings_receive_newsletters',
        appels: 'settings_receive_calls',
        sms: 'settings_receive_sms',
        date: 'created_at',
        statut: 'status',
    };

    if (typeof identifiers[identifier] === 'undefined') {
        throw new Error('Invalid identifier ' + identifier);
    }

    return identifiers[identifier];
}
