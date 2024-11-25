"use client";

import React from "react";
import "./Product.css";
import Link from "next/link";
import Image from "next/image";

const Product = ({ name, price, image, id, hoverImage }) => {
  return (
    <div className="product__container">
      <button>NEW IN</button>
      <div className="product__container-image">
        {/* Default Image */}
        <Link className="default-img" href={`/quickview/${id}`}>
          <Image width={350} height={450} src={image} alt={name} />
        </Link>
        {/* Hover Image */}
        <Link className="hovered-img" href={`/quickview/${id}`}>
          <Image width={350} height={450} src={hoverImage} alt={name} />
        </Link>
      </div>
      <span></span>
      <div className="product__container-details">
        <div className="product__container-details_name">
          <h3>{name}</h3>
          <small>
            N
            {Intl.NumberFormat("en-NG", { maximumFractionDigits: 0 }).format(
              price
            )}
          </small>
        </div>
        <div className="product__container-details_image">
          <img src="/cartImg2.png" alt="Add to cart" />
        </div>
      </div>
    </div>
  );
};

export default Product;
