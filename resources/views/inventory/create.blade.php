@extends('layouts.app')

@section('title', 'Create Product - InApp Inventory Dashboard')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
      <div>
        <h1 class="fs-3 mb-1">Add Inventory</h1>
        <p class="mb-0">Manage your inventory items</p>
      </div>
      <div>
        <a href="{{ route('inventory.index') }}" class="btn btn-primary">Go to Inventory List</a>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body p-4">
        <form id="addProductForm" action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="productName" class="form-label">Product Name</label>
              <input type="text" class="form-control" id="productName" name="name" placeholder="Enter product name" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="productSKU" class="form-label">SKU</label>
              <input type="text" class="form-control" id="productSKU" name="sku" placeholder="Enter SKU" required>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="productPrice" class="form-label">Price</label>
              <input type="number" class="form-control" id="productPrice" name="price" placeholder="0.00" step="0.01" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="productStock" class="form-label">Stock Quantity</label>
              <input type="number" class="form-control" id="productStock" name="stock" placeholder="0" required>
            </div>
          </div>

          <div class="mb-3">
            <label for="productCategory" class="form-label">Category</label>
            <select class="form-select" id="productCategory" name="category" required>
              <option value="">Select category</option>
              <option value="electronics">Electronics</option>
              <option value="clothing">Clothing</option>
              <option value="food">Food</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="productImage" class="form-label">Product Image</label>
            <input type="file" class="form-control" id="productImage" name="image" accept="image/*">
          </div>
          <div class="mb-3">
            <label for="productDescription" class="form-label">Description</label>
            <textarea class="form-control" id="productDescription" name="description" rows="4"
              placeholder="Enter product description"></textarea>
          </div>
          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Add Product</button>
            <button type="reset" class="btn btn-secondary">Clear</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
