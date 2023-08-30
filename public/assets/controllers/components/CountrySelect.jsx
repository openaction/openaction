import React, {useState}  from 'react';
import Select from 'react-select';

const customStyles = {
    control: (provided) => ({
        ...provided,
        cursor: 'pointer',
        borderRadius: 0,
        minHeight: 37,
        minWidth: 100,
    }),
    dropdownIndicator: (provided) => ({
        ...provided,
        padding: 7,
    }),
};

export const CountrySelect = (props) => {
    const [value, setValue] = useState(props.defaultValue);

    return (
        <>
            <Select
                placeholder=""
                value={value}
                onChange={newValue => setValue(newValue)}
                options={props.options}
                styles={customStyles}
            />

            <input type="hidden" name={props.fieldName} value={value.value} />
        </>
    )
}
