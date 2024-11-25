"use client";

import React, { useEffect, useState } from "react";
import Product from "../Product/Product";
import "./Products3.css";

const Products = () => {
  const [products, setProducts] = useState([]);
  const [loading, setLoading] = useState(true); // Loading state
  const [error, setError] = useState(null); // Error state
  const backendBaseUrl = "http://localhost/clash/products/";
  useEffect(() => {
    const fetchProducts = async () => {
      try {
        const response = await fetch(
          "http://localhost/clash/getNewProducts.php"
        );
        const data = await response.json();

        if (data.status === "success") {
          setProducts(data.data); // Set products from API
        } else {
          setError("Failed to fetch products.");
        }
      } catch (err) {
        setError("An error occurred while fetching products.");
      }
    };

    fetchProducts();
  }, []);

  return (
    <div className="products__container">
      {products.map((item) => (
        <div className="products__container-product" key={item.id}>
          <Product
            id={item.id}
            name={item.product_name}
            image={`${backendBaseUrl}${item.image1}`}
            hoverImage={`${backendBaseUrl}${item.image2}`}
            price={item.price}
          />
        </div>
      ))}
    </div>
  );
};

export default Products;
