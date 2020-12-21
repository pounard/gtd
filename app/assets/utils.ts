import { DataResponse, DataQuery } from "foodget/data";

// @todo
const BASE_URI = "http://localhost:8642/api/";

/**
 * We are not going to do anything more with this for now.
 */
export type Uuid = string;

/**
 * Person civilite.
 */
export enum Civilite {
    Monsieur = "monsieur",
    Madame = "madame",
    Mademoiselle = "mademoiselle",
}

/**
 * Just trust the server to speak the same language as us and send the data
 * query as-is.
 */
export function fetchList<T>(route: string, query?: DataQuery<T>): Promise<DataResponse<T>> {
    return fetch(
        BASE_URI + route,
        {
            method: 'POST',
            mode: 'cors',
            cache: 'default',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json'
            },
            redirect: 'follow',
            referrerPolicy: 'no-referrer',
            body: JSON.stringify(query)
        })
        .then(response => response.json())
    ;
}

/**
 * Common base structure for a few models.
 */
export interface WithAddress {
    readonly addrComplement?: string;
    readonly addrLine1?: string;
    readonly addrLine2?: string;
    readonly addrCity?: string;
    readonly addrPostcode?: string;
}
