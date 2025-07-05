import React, { useState, useEffect } from 'react';

export function CategoriesCheckbox({ field, handleCategories, items, ids = [] }) {
    const [categories, setCategories] = useState(Array.from(ids || []));
    const [count, setCount] = useState(0);
    const [checkeds, setCheckeds] = useState({});
    const [dispatch, setDispatch] = useState(false);

    useEffect(() => {
        const checks = {};
        for (const id of categories) {
            checks[id] = true;
        }

        setCheckeds(checks);
    }, []);

    useEffect(() => {
        if (dispatch) {
            handleCategories(categories);
        }
    }, [count, dispatch]);

    const handleCategory = (input, category) => {
        setDispatch(true);

        const isCheck = input.checked;

        if (isCheck) {
            categories.push(parseInt(category[field]));
            setCategories(categories);
            setCount(categories.length + 1);
        } else {
            const index = categories.indexOf(parseInt(category[field]));
            categories.splice(index, 1);
            setCategories(categories);
            setCount(categories.length);
        }
    };

    return (
        <>
            {Array.from(items).map((category) => {
                const id = `category${category[field]}`;

                return (
                    <div className="custom-control custom-checkbox" key={id}>
                        <input
                            type="checkbox"
                            className="custom-control-input"
                            defaultChecked={checkeds[category[field]]}
                            onClick={(ev) => handleCategory(ev.target, category)}
                            id={id}
                        />

                        <label className="custom-control-label" htmlFor={id}>
                            {category.name}
                        </label>
                    </div>
                );
            })}
        </>
    );
}
