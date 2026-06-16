import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../theme/app_theme.dart';
import 'package:provider/provider.dart';
import '../theme/theme_provider.dart';

class OnboardingScreen extends StatefulWidget {
  @override
  _OnboardingScreenState createState() => _OnboardingScreenState();
}

class _OnboardingScreenState extends State<OnboardingScreen> {
  final PageController _pageController = PageController();
  int _currentPage = 0;

  final List<Map<String, dynamic>> _onboardingData = [
    {
      'title': 'Belajar Lebih Seru',
      'description': 'Lognity hadir untuk membuat pengalaman belajar dan kolaborasi kamu dengan mahasiswa lain menjadi sangat menyenangkan.',
      'icon': Icons.rocket_launch_rounded,
      'color': AppTheme.funPurple,
    },
    {
      'title': 'Diskusi Terbuka',
      'description': 'Punya pertanyaan materi? Tanyakan langsung di forum dan dapatkan jawaban dari teman-teman terbaikmu.',
      'icon': Icons.forum_rounded,
      'color': AppTheme.funPink,
    },
    {
      'title': 'Kumpulkan XP & Level',
      'description': 'Jawab pertanyaan, bagikan materi, kumpulkan poin sebanyak-banyaknya untuk meraih level tertinggi di kampus!',
      'icon': Icons.military_tech_rounded,
      'color': Colors.orangeAccent,
    },
  ];

  Future<void> _completeOnboarding() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setBool('has_seen_onboarding', true);
    if (!mounted) return;
    Navigator.pushReplacementNamed(context, '/login');
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Provider.of<ThemeProvider>(context).isDarkMode;
    final bgColors = [
      isDark ? Color(0xFF1E1B4B) : Color(0xFFEEF2FF), // Indigo
      isDark ? Color(0xFF4C1D95) : Color(0xFFFAF5FF), // Purple
      isDark ? Color(0xFF7C2D12) : Color(0xFFFFF7ED), // Orange
    ];

    return Scaffold(
      backgroundColor: bgColors[_currentPage],
      body: SafeArea(
        child: Column(
          children: [
            // Skip Button
            Align(
              alignment: Alignment.topRight,
              child: TextButton(
                onPressed: _completeOnboarding,
                child: Text('Lewati', style: GoogleFonts.poppins(color: _onboardingData[_currentPage]['color'], fontWeight: FontWeight.bold)),
              ),
            ),
            
            // Content
            Expanded(
              child: PageView.builder(
                controller: _pageController,
                onPageChanged: (int page) {
                  setState(() {
                    _currentPage = page;
                  });
                },
                itemCount: _onboardingData.length,
                itemBuilder: (context, index) {
                  return Padding(
                    padding: const EdgeInsets.all(40.0),
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        // Animated Icon Container
                        AnimatedContainer(
                          duration: Duration(milliseconds: 300),
                          padding: EdgeInsets.all(40),
                          decoration: BoxDecoration(
                            color: _onboardingData[index]['color'].withOpacity(0.1),
                            shape: BoxShape.circle,
                            boxShadow: [
                              BoxShadow(
                                color: _onboardingData[index]['color'].withOpacity(0.2),
                                blurRadius: 40,
                                spreadRadius: 10,
                              ),
                            ],
                          ),
                          child: Icon(
                            _onboardingData[index]['icon'],
                            size: 100,
                            color: _onboardingData[index]['color'],
                          ),
                        ),
                        SizedBox(height: 60),
                        // Title
                        Text(
                          _onboardingData[index]['title'],
                          style: GoogleFonts.poppins(
                            fontSize: 28,
                            fontWeight: FontWeight.w800,
                            color: isDark ? Colors.white : AppTheme.lognity900,
                          ),
                          textAlign: TextAlign.center,
                        ),
                        SizedBox(height: 20),
                        // Description
                        Text(
                          _onboardingData[index]['description'],
                          style: GoogleFonts.poppins(
                            fontSize: 16,
                            color: isDark ? Colors.grey.shade300 : Colors.grey.shade600,
                            height: 1.5,
                          ),
                          textAlign: TextAlign.center,
                        ),
                      ],
                    ),
                  );
                },
              ),
            ),

            // Bottom Section (Dots & Button)
            Padding(
              padding: const EdgeInsets.all(40.0),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  // Dot Indicators
                  Row(
                    children: List.generate(
                      _onboardingData.length,
                      (index) => AnimatedContainer(
                        duration: Duration(milliseconds: 300),
                        margin: EdgeInsets.only(right: 8),
                        height: 10,
                        width: _currentPage == index ? 24 : 10,
                        decoration: BoxDecoration(
                          color: _currentPage == index ? _onboardingData[_currentPage]['color'] : Colors.grey.withOpacity(0.3),
                          borderRadius: BorderRadius.circular(5),
                        ),
                      ),
                    ),
                  ),
                  
                  // Next / Get Started Button
                  InkWell(
                    onTap: () {
                      if (_currentPage == _onboardingData.length - 1) {
                        _completeOnboarding();
                      } else {
                        _pageController.nextPage(
                          duration: Duration(milliseconds: 300),
                          curve: Curves.easeInOut,
                        );
                      }
                    },
                    borderRadius: BorderRadius.circular(30),
                    child: AnimatedContainer(
                      duration: Duration(milliseconds: 300),
                      padding: EdgeInsets.symmetric(horizontal: 24, vertical: 14),
                      decoration: BoxDecoration(
                        color: _onboardingData[_currentPage]['color'],
                        borderRadius: BorderRadius.circular(30),
                        boxShadow: [
                          BoxShadow(
                            color: _onboardingData[_currentPage]['color'].withOpacity(0.4),
                            blurRadius: 10,
                            offset: Offset(0, 4),
                          )
                        ],
                      ),
                      child: Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Text(
                            _currentPage == _onboardingData.length - 1 ? 'Mulai' : 'Lanjut',
                            style: GoogleFonts.poppins(
                              color: Colors.white,
                              fontWeight: FontWeight.bold,
                              fontSize: 16,
                            ),
                          ),
                          if (_currentPage != _onboardingData.length - 1) ...[
                            SizedBox(width: 8),
                            Icon(Icons.arrow_forward_rounded, color: Colors.white, size: 20),
                          ]
                        ],
                      ),
                    ),
                  ),
                ],
              ),
            )
          ],
        ),
      ),
    );
  }
}
