// "use client";

// import React from "react";
// import Product from "../Product/Product";
// import "./Products.css";

// const Products = ({ product }) => {
//   const DUMMY_PRODUCTS = [
//     {
//       id: 1,
//       name: "Summer Sleeveless Tank ‘25  ",
//       image: "/product1.png",
//       price: "N100,000",
//       hoverImage: "/product1b.png",
//     },
//     {
//       id: 2,
//       name: "Summer Sleeveless Tank ‘25  ",
//       image: "/product3.png",
//       hoverImage: "/product3b.png",
//       price: "N100,000",
//     },
//     {
//       id: 3,
//       name: "Summer Sleeveless Tank ‘25  ",
//       image: "/product4.png",
//       hoverImage: "/product4.png",
//       price: "N100,000",
//     },
//     {
//       id: 4,
//       name: "Summer Sleeveless Tank ‘25  ",
//       image: "/product5.png",
//       hoverImage: "/product5.png",
//       price: "N100,000",
//     },
//     {
//       id: 5,
//       name: "Summer Sleeveless Tank ‘25  ",
//       image: "/product6.png",
//       hoverImage: "/product6.png",
//       price: "N100,000",
//     },
//     {
//       id: 6,
//       name: "Summer Sleeveless Tank ‘25  ",
//       image: "/product7.png",
//       hoverImage: "/product7.png",
//       price: "N100,000",
//     },
//   ];
//   // console.log(DUMMY_PRODUCTS)
//   return (
//     <div className="products__container">
//       {DUMMY_PRODUCTS.map((item) => {
//         return (
//           <div className="products__container-product" key={item.id}>
//             <Product
//               id={item.id}
//               name={item.name}
//               image={item.image}
//               hoverImage={item.hoverImage}
//               price={item.price}
//             />
//           </div>
//         );
//       })}
//     </div>
//   );
// };

// export default Products;

"use client";

import React, { useEffect, useState } from "react";
import Product from "../Product/Product";
import "./Products.css";

const Products = () => {
  const [products, setProducts] = useState([]);
  const [loading, setLoading] = useState(true); // Loading state
  const [error, setError] = useState(null); // Error state
  const backendBaseUrl = "http://localhost/clash/products/";
  useEffect(() => {
    const fetchProducts = async () => {
      try {
        const response = await fetch(
          "http://localhost/clash/getAllProducts.php"
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
