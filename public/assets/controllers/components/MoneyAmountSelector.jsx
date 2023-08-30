import React, {useState} from 'react';

export const MoneyAmountSelector = (props) => {
    const [amount, setAmount] = useState(parseInt(props.defaultValue));

    return (
        <>
            <div className="row mb-3">
                {props.suggestions.map((suggestion) => (
                    <div className="col-2">
                        <button type="button" className="btn btn-secondary" onClick={() => setAmount(parseInt(suggestion))}>
                            {suggestion}
                        </button>
                    </div>
                ))}
            </div>

            <div>
                <small className="text-muted">{props.customLabel}</small>
            </div>
            <input
                type="number"
                className="form-control"
                name={props.fieldName}
                value={amount || ''}
                onChange={(e) => setAmount(parseInt(e.target.value))}
            />
        </>
    )
}
