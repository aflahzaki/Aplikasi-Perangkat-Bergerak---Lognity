import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart';
import 'package:google_fonts/google_fonts.dart';
import 'dart:ui';
import '../../services/api_service.dart';
import '../../theme/app_theme.dart';

class LibraryIndexScreen extends StatefulWidget {
  @override
  _LibraryIndexScreenState createState() => _LibraryIndexScreenState();
}

class _LibraryIndexScreenState extends State<LibraryIndexScreen> with SingleTickerProviderStateMixin {
  List<dynamic> ebooks = [];
  bool isLoading = true;
  bool _isGridView = true;

  final TextEditingController _searchController = TextEditingController();
  String _selectedCategory = '';

  int _page = 1;
  bool _hasMore = true;
  bool _isLoadingMore = false;
  final ScrollController _scrollController = ScrollController();

  late AnimationController _animController;
  late Animation<double> _fadeAnim;

  List<Map<String, dynamic>> _categories = [
    {'value': '', 'label': 'Semua', 'icon': Icons.apps_rounded},
  ];

  @override
  void initState() {
    super.initState();
    _animController = AnimationController(vsync: this, duration: const Duration(milliseconds: 600));
    _fadeAnim = CurvedAnimation(parent: _animController, curve: Curves.easeOut);
    _animController.forward();
    _fetchMetadata();
    _fetchLibrary();
    _scrollController.addListener(() {
      if (_scrollController.position.pixels >= _scrollController.position.maxScrollExtent - 150) {
        _loadMore();
      }
    });
  }

  Future<void> _fetchMetadata() async {
    try {
      final data = await ApiService.getMetadata();
      final libCats = data['categories']['library'] ?? [];
      setState(() {
        _categories = [{'value': '', 'label': 'Semua', 'icon': Icons.apps_rounded}];
        for (var c in libCats) {
          _categories.add({'value': c, 'label': c, 'icon': Icons.menu_book_rounded});
        }
      });
    } catch (e) {
      // ignore
    }
  }

  @override
  void dispose() {
    _animController.dispose();
    _scrollController.dispose();
    _searchController.dispose();
    super.dispose();
  }

  Future<void> _fetchLibrary({bool refresh = true}) async {
    if (refresh) {
      setState(() { isLoading = true; _page = 1; _hasMore = true; });
    }
    try {
      final response = await ApiService.getLibrary(
        page: _page, 
        search: _searchController.text,
        category: _selectedCategory,
      );
      final List newBooks = response['data'] ?? [];
      setState(() {
        if (refresh) { ebooks = newBooks; } else { ebooks.addAll(newBooks); }
        _hasMore = newBooks.length >= 10;
      });
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Gagal memuat perpustakaan'),
            backgroundColor: Colors.red.shade400,
            behavior: SnackBarBehavior.floating,
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
          ),
        );
      }
    } finally {
      if (mounted) setState(() { isLoading = false; _isLoadingMore = false; });
    }
  }

  Future<void> _loadMore() async {
    if (_hasMore && !_isLoadingMore && !isLoading) {
      setState(() => _isLoadingMore = true);
      _page++;
      await _fetchLibrary(refresh: false);
    }
  }

  void _openBook(Map<String, dynamic> book) async {
    if (book['file_url'] != null) {
      final url = Uri.parse(ApiService.parseUrl(book['file_url']));
      if (await canLaunchUrl(url)) {
        await launchUrl(url, mode: LaunchMode.externalApplication);
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Tidak dapat membuka tautan ini'),
            behavior: SnackBarBehavior.floating,
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
          ),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return Scaffold(
      body: FadeTransition(
        opacity: _fadeAnim,
        child: Stack(
          children: [
            // ── Animated Background Orbs ──
            Positioned(
              top: -50,
              left: -50,
              child: Container(
                width: 300,
                height: 300,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  color: AppTheme.lognity500.withOpacity(isDark ? 0.15 : 0.1),
                ),
              ),
            ),
            Positioned(
              bottom: 100,
              right: -100,
              child: Container(
                width: 250,
                height: 250,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  color: AppTheme.funPink.withOpacity(isDark ? 0.2 : 0.1),
                ),
              ),
            ),
            Positioned.fill(
              child: BackdropFilter(
                filter: ImageFilter.blur(sigmaX: 40, sigmaY: 40),
                child: Container(color: Colors.transparent),
              ),
            ),
            Column(
              children: [
            // ── Search & Filter Header ──
            _buildSearchHeader(isDark),

            // ── Category Chips ──
            _buildCategoryChips(isDark),

            // ── Grid/List Toggle + Count ──
            _buildToolbar(isDark),

            // ── Book Grid/List ──
              Expanded(child: _buildBookContent(isDark)),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildSearchHeader(bool isDark) {
    return Container(
      padding: const EdgeInsets.fromLTRB(16, 12, 16, 12),
      decoration: AppTheme.glassDecoration(isDark, borderRadius: 0).copyWith(
        border: null,
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(isDark ? 0.2 : 0.05), blurRadius: 8, offset: const Offset(0, 2))],
      ),
      child: Row(
        children: [
          Expanded(
            child: TextField(
              controller: _searchController,
              decoration: InputDecoration(
                hintText: 'Cari judul atau penulis...',
                hintStyle: GoogleFonts.poppins(fontSize: 13, color: Colors.grey.shade500),
                prefixIcon: Icon(Icons.search_rounded, size: 20, color: AppTheme.lognity500),
                suffixIcon: _searchController.text.isNotEmpty
                    ? IconButton(
                        icon: Icon(Icons.clear_rounded, size: 18, color: Colors.grey.shade500),
                        onPressed: () { _searchController.clear(); _fetchLibrary(); },
                      )
                    : null,
                isDense: true,
                contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                filled: true,
                fillColor: isDark ? Colors.grey.shade800 : Colors.grey.shade50,
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(14), borderSide: BorderSide.none),
                focusedBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(14),
                  borderSide: BorderSide(color: AppTheme.lognity500, width: 1.5),
                ),
              ),
              onSubmitted: (_) => _fetchLibrary(),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildCategoryChips(bool isDark) {
    return SizedBox(
      height: 52,
      child: ListView.builder(
        scrollDirection: Axis.horizontal,
        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
        itemCount: _categories.length,
        itemBuilder: (context, index) {
          final cat = _categories[index];
          final isSelected = _selectedCategory == cat['value'];
          return Padding(
            padding: const EdgeInsets.only(right: 8),
            child: GestureDetector(
              onTap: () {
                setState(() => _selectedCategory = cat['value']);
                _fetchLibrary();
              },
              child: AnimatedContainer(
                duration: const Duration(milliseconds: 200),
                padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 6),
                decoration: BoxDecoration(
                  gradient: isSelected ? AppTheme.primaryGradient : null,
                  color: isSelected ? null : (isDark ? Colors.grey.shade800 : Colors.grey.shade100),
                  borderRadius: BorderRadius.circular(20),
                  border: Border.all(
                    color: isSelected ? Colors.transparent : (isDark ? Colors.grey.shade700 : Colors.grey.shade300),
                  ),
                  boxShadow: isSelected
                      ? [BoxShadow(color: AppTheme.lognity600.withOpacity(0.3), blurRadius: 8, offset: const Offset(0, 2))]
                      : [],
                ),
                child: Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Icon(cat['icon'] as IconData, size: 16,
                      color: isSelected ? Colors.white : (isDark ? Colors.grey.shade400 : Colors.grey.shade600)),
                    const SizedBox(width: 6),
                    Text(cat['label'],
                      style: GoogleFonts.poppins(
                        fontSize: 12, fontWeight: FontWeight.w600,
                        color: isSelected ? Colors.white : (isDark ? Colors.grey.shade400 : Colors.grey.shade600),
                      ),
                    ),
                  ],
                ),
              ),
            ),
          );
        },
      ),
    );
  }

  Widget _buildToolbar(bool isDark) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(6),
            decoration: BoxDecoration(
              color: AppTheme.lognity500.withOpacity(0.1),
              borderRadius: BorderRadius.circular(8),
            ),
            child: const Icon(Icons.menu_book_rounded, size: 16, color: AppTheme.lognity600),
          ),
          const SizedBox(width: 8),
          Text(
            '${ebooks.length} E-Book',
            style: GoogleFonts.poppins(fontSize: 13, fontWeight: FontWeight.w600, color: isDark ? Colors.white : Colors.grey.shade700),
          ),
          const Spacer(),
          GestureDetector(
            onTap: () => setState(() => _isGridView = !_isGridView),
            child: AnimatedContainer(
              duration: const Duration(milliseconds: 200),
              padding: const EdgeInsets.all(8),
              decoration: BoxDecoration(
                color: isDark ? Colors.grey.shade800 : Colors.grey.shade100,
                borderRadius: BorderRadius.circular(10),
                border: Border.all(color: isDark ? Colors.grey.shade700 : Colors.grey.shade300),
              ),
              child: Icon(
                _isGridView ? Icons.grid_view_rounded : Icons.view_list_rounded,
                size: 18,
                color: AppTheme.lognity500,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildBookContent(bool isDark) {
    if (isLoading) {
      return _buildLoadingShimmer(isDark);
    }

    if (ebooks.isEmpty) {
      return _buildEmptyState(isDark);
    }

    return RefreshIndicator(
      color: AppTheme.lognity500,
      onRefresh: () => _fetchLibrary(),
      child: _isGridView ? _buildGridView(isDark) : _buildListView(isDark),
    );
  }

  Widget _buildLoadingShimmer(bool isDark) {
    return GridView.builder(
      padding: const EdgeInsets.all(16),
      gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
        crossAxisCount: 2,
        crossAxisSpacing: 14,
        mainAxisSpacing: 14,
        childAspectRatio: 0.62,
      ),
      itemCount: 6,
      itemBuilder: (context, index) {
        return Container(
          decoration: BoxDecoration(
            color: isDark ? AppTheme.cardDark : Colors.white,
            borderRadius: BorderRadius.circular(20),
            border: Border.all(color: isDark ? Colors.grey.shade800 : Colors.grey.shade200),
          ),
          child: Column(
            children: [
              Expanded(
                flex: 3,
                child: Container(
                  decoration: BoxDecoration(
                    color: isDark ? Colors.grey.shade800 : Colors.grey.shade200,
                    borderRadius: const BorderRadius.only(
                      topLeft: Radius.circular(20),
                      topRight: Radius.circular(20),
                    ),
                  ),
                  child: Center(
                    child: Icon(Icons.book_rounded, size: 40, color: isDark ? Colors.grey.shade700 : Colors.grey.shade300),
                  ),
                ),
              ),
              Expanded(
                flex: 2,
                child: Padding(
                  padding: const EdgeInsets.all(12),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Container(height: 12, width: double.infinity, decoration: BoxDecoration(color: isDark ? Colors.grey.shade700 : Colors.grey.shade200, borderRadius: BorderRadius.circular(6))),
                      const SizedBox(height: 8),
                      Container(height: 10, width: 80, decoration: BoxDecoration(color: isDark ? Colors.grey.shade700 : Colors.grey.shade200, borderRadius: BorderRadius.circular(6))),
                    ],
                  ),
                ),
              ),
            ],
          ),
        );
      },
    );
  }

  Widget _buildEmptyState(bool isDark) {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Container(
            width: 80, height: 80,
            decoration: BoxDecoration(
              gradient: LinearGradient(colors: [AppTheme.lognity100, AppTheme.lognity200]),
              borderRadius: BorderRadius.circular(24),
            ),
            child: const Center(child: Text('📚', style: TextStyle(fontSize: 36))),
          ),
          const SizedBox(height: 20),
          Text('Belum ada e-book', style: GoogleFonts.poppins(fontSize: 18, fontWeight: FontWeight.w600, color: isDark ? Colors.white : Colors.grey.shade700)),
          const SizedBox(height: 8),
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 48),
            child: Text(
              _searchController.text.isNotEmpty || _selectedCategory.isNotEmpty
                  ? 'Tidak ada hasil untuk pencarian ini. Coba kata kunci lain.'
                  : 'E-book akan muncul di sini setelah admin mengunggahnya.',
              textAlign: TextAlign.center,
              style: GoogleFonts.poppins(fontSize: 13, color: Colors.grey.shade500),
            ),
          ),
          if (_searchController.text.isNotEmpty || _selectedCategory.isNotEmpty) ...[
            const SizedBox(height: 20),
            GestureDetector(
              onTap: () {
                _searchController.clear();
                setState(() => _selectedCategory = '');
                _fetchLibrary();
              },
              child: Container(
                padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 10),
                decoration: BoxDecoration(
                  gradient: AppTheme.primaryGradient,
                  borderRadius: BorderRadius.circular(14),
                  boxShadow: [BoxShadow(color: AppTheme.lognity600.withOpacity(0.3), blurRadius: 8, offset: const Offset(0, 2))],
                ),
                child: Text('Reset Filter', style: GoogleFonts.poppins(fontSize: 13, fontWeight: FontWeight.w600, color: Colors.white)),
              ),
            ),
          ],
        ],
      ),
    );
  }

  Widget _buildGridView(bool isDark) {
    return GridView.builder(
      controller: _scrollController,
      padding: const EdgeInsets.fromLTRB(16, 8, 16, 80),
      gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
        crossAxisCount: 2,
        crossAxisSpacing: 14,
        mainAxisSpacing: 14,
        childAspectRatio: 0.58,
      ),
      itemCount: ebooks.length + (_isLoadingMore ? 2 : 0),
      itemBuilder: (context, index) {
        if (index >= ebooks.length) {
          return const Center(child: Padding(padding: EdgeInsets.all(16), child: CircularProgressIndicator(strokeWidth: 2)));
        }
        return _buildBookGridCard(ebooks[index], isDark);
      },
    );
  }

  Widget _buildListView(bool isDark) {
    return ListView.builder(
      controller: _scrollController,
      padding: const EdgeInsets.fromLTRB(16, 8, 16, 80),
      itemCount: ebooks.length + (_isLoadingMore ? 1 : 0),
      itemBuilder: (context, index) {
        if (index >= ebooks.length) {
          return const Center(child: Padding(padding: EdgeInsets.symmetric(vertical: 16), child: CircularProgressIndicator(strokeWidth: 2)));
        }
        return _buildBookListCard(ebooks[index], isDark);
      },
    );
  }

  Widget _buildBookGridCard(Map<String, dynamic> book, bool isDark) {
    final String coverUrl = book['cover_url'] != null ? ApiService.parseUrl(book['cover_url']) : '';
    final String category = book['category'] ?? '';

    return GestureDetector(
      onTap: () => _openBook(book),
      child: Container(
        decoration: AppTheme.glassDecoration(isDark, borderRadius: 20).copyWith(
          boxShadow: [
            BoxShadow(color: Colors.black.withOpacity(isDark ? 0.2 : 0.06), blurRadius: 12, offset: const Offset(0, 4)),
          ],
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            // Cover Image
            Expanded(
              flex: 3,
              child: Stack(
                children: [
                  ClipRRect(
                    borderRadius: const BorderRadius.only(
                      topLeft: Radius.circular(20),
                      topRight: Radius.circular(20),
                    ),
                    child: coverUrl.isNotEmpty
                        ? Image.network(
                            coverUrl,
                            fit: BoxFit.cover,
                            width: double.infinity,
                            height: double.infinity,
                            errorBuilder: (c, e, s) => _coverPlaceholder(isDark),
                          )
                        : _coverPlaceholder(isDark),
                  ),
                  // Category badge
                  if (category.isNotEmpty)
                    Positioned(
                      top: 8, left: 8,
                      child: Container(
                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                        decoration: BoxDecoration(
                          color: AppTheme.lognity600.withOpacity(0.9),
                          borderRadius: BorderRadius.circular(8),
                        ),
                        child: Text(category,
                          style: GoogleFonts.poppins(fontSize: 9, fontWeight: FontWeight.w600, color: Colors.white)),
                      ),
                    ),
                  // Open indicator
                  Positioned(
                    bottom: 8, right: 8,
                    child: Container(
                      padding: const EdgeInsets.all(6),
                      decoration: BoxDecoration(
                        color: Colors.black.withOpacity(0.5),
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: const Icon(Icons.open_in_new_rounded, size: 14, color: Colors.white),
                    ),
                  ),
                ],
              ),
            ),
            // Book Info
            Expanded(
              flex: 2,
              child: Padding(
                padding: const EdgeInsets.fromLTRB(12, 10, 12, 10),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Expanded(
                      child: Text(
                        book['title'] ?? 'Tanpa Judul',
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                        style: GoogleFonts.poppins(
                          fontWeight: FontWeight.w600, fontSize: 13,
                          color: isDark ? Colors.white : Colors.grey.shade800,
                          height: 1.3,
                        ),
                      ),
                    ),
                    const SizedBox(height: 4),
                    Row(
                      children: [
                        Icon(Icons.person_outline_rounded, size: 13, color: AppTheme.lognity500),
                        const SizedBox(width: 4),
                        Expanded(
                          child: Text(
                            book['author'] ?? 'Penulis tidak diketahui',
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                            style: GoogleFonts.poppins(
                              color: AppTheme.lognity600, fontSize: 11,
                              fontWeight: FontWeight.w500,
                            ),
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildBookListCard(Map<String, dynamic> book, bool isDark) {
    final String coverUrl = book['cover_url'] != null ? ApiService.parseUrl(book['cover_url']) : '';
    final String category = book['category'] ?? '';

    return GestureDetector(
      onTap: () => _openBook(book),
      child: Container(
        margin: const EdgeInsets.only(bottom: 12),
        decoration: AppTheme.glassDecoration(isDark, borderRadius: 16),
        child: IntrinsicHeight(
          child: Row(
            children: [
              // Cover thumbnail
              ClipRRect(
                borderRadius: const BorderRadius.only(
                  topLeft: Radius.circular(16),
                  bottomLeft: Radius.circular(16),
                ),
                child: SizedBox(
                  width: 90, height: 120,
                  child: coverUrl.isNotEmpty
                      ? Image.network(coverUrl, fit: BoxFit.cover,
                          errorBuilder: (c, e, s) => _coverPlaceholderSmall(isDark))
                      : _coverPlaceholderSmall(isDark),
                ),
              ),
              // Book info
              Expanded(
                child: Padding(
                  padding: const EdgeInsets.all(14),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      if (category.isNotEmpty)
                        Container(
                          margin: const EdgeInsets.only(bottom: 6),
                          padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                          decoration: BoxDecoration(
                            color: AppTheme.lognity500.withOpacity(0.1),
                            borderRadius: BorderRadius.circular(6),
                          ),
                          child: Text(category,
                            style: GoogleFonts.poppins(fontSize: 10, fontWeight: FontWeight.w600, color: AppTheme.lognity600)),
                        ),
                      Text(
                        book['title'] ?? 'Tanpa Judul',
                        maxLines: 2, overflow: TextOverflow.ellipsis,
                        style: GoogleFonts.poppins(fontWeight: FontWeight.w600, fontSize: 14, color: isDark ? Colors.white : Colors.grey.shade800),
                      ),
                      const SizedBox(height: 6),
                      Row(
                        children: [
                          Icon(Icons.person_outline_rounded, size: 14, color: AppTheme.lognity500),
                          const SizedBox(width: 4),
                          Expanded(
                            child: Text(
                              book['author'] ?? 'Penulis tidak diketahui',
                              maxLines: 1, overflow: TextOverflow.ellipsis,
                              style: GoogleFonts.poppins(color: AppTheme.lognity600, fontSize: 12, fontWeight: FontWeight.w500),
                            ),
                          ),
                        ],
                      ),
                    ],
                  ),
                ),
              ),
              // Action icon
              Padding(
                padding: const EdgeInsets.only(right: 12),
                child: Container(
                  padding: const EdgeInsets.all(8),
                  decoration: BoxDecoration(
                    color: AppTheme.lognity500.withOpacity(0.08),
                    borderRadius: BorderRadius.circular(10),
                  ),
                  child: const Icon(Icons.open_in_new_rounded, size: 16, color: AppTheme.lognity600),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _coverPlaceholder(bool isDark) {
    return Container(
      width: double.infinity,
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: isDark
              ? [Colors.grey.shade800, Colors.grey.shade700]
              : [AppTheme.lognity50, AppTheme.lognity100],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
      ),
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(Icons.menu_book_rounded, size: 44, color: isDark ? Colors.grey.shade600 : AppTheme.lognity300),
          const SizedBox(height: 4),
          Text('E-Book', style: GoogleFonts.poppins(fontSize: 10, color: isDark ? Colors.grey.shade600 : AppTheme.lognity400, fontWeight: FontWeight.w500)),
        ],
      ),
    );
  }

  Widget _coverPlaceholderSmall(bool isDark) {
    return Container(
      color: isDark ? Colors.grey.shade800 : AppTheme.lognity50,
      child: Center(
        child: Icon(Icons.menu_book_rounded, size: 32, color: isDark ? Colors.grey.shade600 : AppTheme.lognity300),
      ),
    );
  }
}
